<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\DocumentType;
use Illuminate\Support\Collection;

class BusinessDocumentRequirementService
{
    /**
     * @return Collection<int, DocumentType>
     */
    public function applicableTypes(Business $business): Collection
    {
        return DocumentType::query()
            ->active()
            ->applicableTo($business->country_code)
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{
     *     items: Collection<int, array{type: DocumentType, document: BusinessDocument|null, uploaded: bool}>,
     *     required_count: int,
     *     uploaded_required_count: int,
     *     missing_required: Collection<int, DocumentType>,
     *     completion_percentage: int
     * }
     */
    public function summary(Business $business): array
    {
        $types = $this->applicableTypes($business);
        $documentsByType = $business->documents()
            ->latest('id')
            ->get()
            ->unique('document_type_id')
            ->keyBy('document_type_id');

        $items = $types->map(function (DocumentType $type) use ($documentsByType): array {
            $document = $documentsByType->get($type->id);

            return [
                'type' => $type,
                'document' => $document,
                'uploaded' => $document !== null,
            ];
        });

        $requiredItems = $items->filter(fn (array $item): bool => $item['type']->is_required);
        $uploadedRequiredCount = $requiredItems->where('uploaded', true)->count();
        $requiredCount = $requiredItems->count();

        return [
            'items' => $items,
            'required_count' => $requiredCount,
            'uploaded_required_count' => $uploadedRequiredCount,
            'missing_required' => $requiredItems
                ->where('uploaded', false)
                ->pluck('type')
                ->values(),
            'completion_percentage' => $requiredCount === 0
                ? 100
                : (int) round(($uploadedRequiredCount / $requiredCount) * 100),
        ];
    }
}
