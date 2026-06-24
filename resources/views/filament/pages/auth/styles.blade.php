<style>
/* UNLOCK Simple Layout Constraints */
.fi-simple-main {
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    min-height: 100vh !important;
    background-color: transparent !important;
}
.fi-simple-section {
    width: 100% !important;
    max-width: none !important;
    padding: 1rem !important;
    display: flex !important;
    justify-content: center !important;
    background-color: transparent !important;
}
/* Hide Default Header/Logo from simple page */
.fi-simple-header {
    display: none !important;
}
/* Hide default footer */
.fi-simple-footer {
    display: none !important;
}

/* Hide default background elements if any and FORCE WHITE */
body, .fi-body, html {
    background-color: #ffffff !important;
}

/* Ensure dark mode also respects this */
:root .fi-simple-layout {
    background-color: #ffffff !important;
}
.dark body, .dark .fi-body, .dark .fi-simple-layout {
    background-color: #ffffff !important;
}

/* Scoped Styles for this Page */
.login-container {
    display: flex;
    width: 100%;
    justify-content: center;
}
.login-card-custom {
    background-color: white;
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    width: 100%;
    max-width: 64rem;
    display: flex;
    flex-direction: column;
}

.left-col {
    width: 100%;
    padding: 2.5rem;
}

.right-col {
    display: none;
    width: 50%;
    background-color: #ffffff;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 2rem;
    border-left: 1px solid #f3f4f6;
}
.right-col img {
    max-width: 90%;
    height: auto;
    object-fit: contain;
}

@media (min-width: 1024px) {
    .login-card-custom {
        flex-direction: row;
    }
    .left-col {
        width: 50%;
        padding: 4rem;
    }
    .right-col {
        display: flex;
    }
}

/* FORCE OVERRIDES - Scoped to this container */
.fi-simple-main-ctn, h2, p, label, span {
    color: #111827 !important;
    --tw-text-opacity: 1 !important;
}

/* Inputs */
input[type="email"], input[type="password"], input[type="text"] {
    background-color: #ffffff !important;
    border: 1px solid #1f2937 !important;
    color: #000000 !important;
    width: 100% !important;
    min-width: 0 !important;
}
input:focus {
    border-color: #f59e0b !important;
    ring: 2px solid #f59e0b !important;
}

/* Icons */
.fi-input-wrp .h-5, .fi-input-wrp .w-5 {
    color: #000000 !important;
}
button[title="Show password"] svg, button[title="Hide password"] svg {
    color: #000000 !important;
}

/* Submit Button */
button[type="submit"], .fi-btn {
    background-color: #f59e0b !important;
    color: white !important;
    opacity: 1 !important;
    visibility: visible !important;
    border-radius: 0.5rem;
}
button[type="submit"]:hover {
    background-color: #d97706 !important;
}

/* Checkbox */
input[type="checkbox"] {
    appearance: none;
    background-color: #fff;
    margin: 0;
    font: inherit;
    color: currentColor;
    width: 1.15em;
    height: 1.15em;
    border: 1px solid #d1d5db !important;
    border-radius: 0.15em;
    display: grid;
    place-content: center;
}
input[type="checkbox"]::before {
    content: "";
    width: 0.65em;
    height: 0.65em;
    transform: scale(0);
    transition: 120ms transform ease-in-out;
    box-shadow: inset 1em 1em #f59e0b;
    transform-origin: center;
    clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
}
input[type="checkbox"]:checked::before {
    transform: scale(1);
}
input[type="checkbox"]:checked {
    border-color: #f59e0b !important;
}
</style>
