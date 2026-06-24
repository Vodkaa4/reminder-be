<style>
    /* Clean Light Gray Background */
    body, .fi-body, .fi-layout {
        background-color: #f8fafc !important; /* bg-slate-50 */
    }

    /* Clean Sidebar */
    aside.fi-sidebar {
        background-color: #ffffff !important;
        border-right: 1px solid #e2e8f0 !important; /* border-slate-200 */
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02) !important;
        z-index: 20 !important;
    }

    /* Clean Topbar */
    header.fi-topbar {
        background-color: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02) !important;
    }

    /* Professional Card & Widget Styling */
    .fi-section, .fi-widget {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -2px rgba(0, 0, 0, 0.02) !important;
        border-radius: 0.75rem !important; /* 12px rounded borders */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .fi-section:hover, .fi-widget:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.02) !important;
        transform: translateY(-2px);
    }

    /* Table Hover Effect */
    .fi-ta-record {
        transition: background-color 0.15s ease;
    }
    .fi-ta-record:hover {
        background-color: #f1f5f9 !important; /* bg-slate-100 */
    }

    /* Modern Buttons */
    .fi-btn {
        border-radius: 0.5rem !important; /* 8px rounded borders */
        font-weight: 500 !important;
        letter-spacing: 0.01em !important;
        transition: all 0.2s ease;
    }
    
    .fi-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
    }

    /* Input Fields styling */
    .fi-input-wrapper {
        border-radius: 0.5rem !important;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .fi-input-wrapper:focus-within {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important; /* Indigo focus ring */
    }

    /* Modal Styling */
    .fi-modal-window {
        border-radius: 1rem !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
    }
</style>
