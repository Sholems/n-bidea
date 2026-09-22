<style>
    :root {
        --ncci-primary: #063d1f;
        --ncci-secondary: #0f6b35;
        --ncci-accent: #ffd21e;
        --ncci-red: #d9270f;
        --ncci-ink: #172033;
        --ncci-muted: #667085;
        --ncci-soft: #f4f7f1;
        --ncci-border: #dce5dc;
        --ncci-dark: #082414;
        --ncci-light: #e7f1e9;
    }
    html { overflow-x: hidden; }
    body { color: var(--ncci-ink); }
    a { color: var(--ncci-secondary); }
    a:hover { color: var(--ncci-primary); }
    .btn { border-radius: 0.35rem; font-weight: 600; }
    .btn-primary,
    .btn-ncci,
    .bg-primary {
        background-color: var(--ncci-primary) !important;
        border-color: var(--ncci-primary) !important;
        color: #fff !important;
    }
    .btn-primary:hover,
    .btn-primary:focus,
    .btn-ncci:hover,
    .btn-ncci:focus {
        background-color: var(--ncci-secondary) !important;
        border-color: var(--ncci-secondary) !important;
    }
    .btn-outline-primary {
        border-color: var(--ncci-primary);
        color: var(--ncci-primary);
    }
    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background-color: var(--ncci-primary);
        border-color: var(--ncci-primary);
        color: #fff;
    }
    .text-primary { color: var(--ncci-primary) !important; }
    .border-primary { border-color: var(--ncci-primary) !important; }
    .card { border-radius: 0.4rem; }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--ncci-secondary);
        box-shadow: 0 0 0 0.2rem rgba(15, 107, 53, 0.16);
    }
    :focus-visible {
        outline: 3px solid rgba(255, 210, 30, 0.85);
        outline-offset: 2px;
    }
    .brand-logo {
        display: inline-flex;
        flex: 0 0 auto;
        overflow: hidden;
        background-color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.42);
        border-radius: 0.25rem;
        line-height: 0;
    }
    .brand-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center 48%;
    }
    .brand-logo--header {
        width: 10rem;
        height: 3.75rem;
    }
    .brand-logo--sidebar {
        width: 13.25rem;
        height: 5rem;
    }
    .brand-logo--footer {
        width: 12rem;
        height: 4.5rem;
    }
    .brand-logo--auth {
        width: min(100%, 17.5rem);
        height: 6.5rem;
        border-color: var(--ncci-border);
    }
    @media (max-width: 575.98px) {
        .brand-logo--header {
            width: 8.75rem;
            height: 3.25rem;
        }
        .brand-logo--auth {
            width: min(100%, 15rem);
            height: 5.625rem;
        }
    }
</style>
