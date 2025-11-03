<template>
<svg v-if="name === 'cheveron-down'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
<svg v-else-if="name === 'submit-doc'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-6">
    <path d="M11.47 1.72a.75.75 0 0 1 1.06 0l3 3a.75.75 0 0 1-1.06 1.06l-1.72-1.72V7.5h-1.5V4.06L9.53 5.78a.75.75 0 0 1-1.06-1.06l3-3ZM11.25 7.5V15a.75.75 0 0 0 1.5 0V7.5h3.75a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3h-9a3 3 0 0 1-3-3v-9a3 3 0 0 1 3-3h3.75Z" />
</svg>
<svg v-else-if="name === 'fill-out'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-6">
    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
</svg>
<svg v-else-if="name === 'wait'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-6">
    <path fillRule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clipRule="evenodd" />
</svg>
<svg v-else-if="name === 'confirm'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-6">
    <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
    <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
</svg>
<svg v-else-if="name === 'cheveron-right'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
    <polygon points="12.95 10.707 13.657 10 8 4.343 6.586 5.757 10.828 10 6.586 14.243 8 15.657 12.95 10.707" /></svg>
<svg v-else-if="name === 'dashboard'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
    <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm-5.6-4.29a9.95 9.95 0 0 1 11.2 0 8 8 0 1 0-11.2 0zm6.12-7.64l3.02-3.02 1.41 1.41-3.02 3.02a2 2 0 1 1-1.41-1.41z" /></svg>
<svg v-else-if="name === 'office'" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
    <path fill-rule="evenodd" d="M7 0h86v100H57.108V88.418H42.892V100H7V0zm9 64h11v15H16V64zm57 0h11v15H73V64zm-19 0h11v15H54V64zm-19 0h11v15H35V64zM16 37h11v15H16V37zm57 0h11v15H73V37zm-19 0h11v15H54V37zm-19 0h11v15H35V37zM16 11h11v15H16V11zm57 0h11v15H73V11zm-19 0h11v15H54V11zm-19 0h11v15H35V11z" /></svg>
<svg v-else-if="name === 'printer'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
    <path d="M4 16H0V6h20v10h-4v4H4v-4zm2-4v6h8v-6H6zM4 0h12v5H4V0zM2 8v2h2V8H2zm4 0v2h2V8H6z" /></svg>
<svg v-else-if="name === 'trash'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
    <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z" /></svg>
<svg v-else-if="name === 'users'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
    <path d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z" /></svg>
<svg v-else-if="name === 'patients'" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="#000000">
    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    <g id="SVGRepo_iconCarrier">
        <g id="Layer_2" data-name="Layer 2">
            <g id="invisible_box" data-name="invisible box">
                <rect width="48" height="48" fill="none"></rect>
            </g>
            <g id="Medical">
                <g>
                    <path d="M45.2,33.3a17.4,17.4,0,0,0-20.4,0,1.7,1.7,0,0,0-.8,1.4v5.8A1.6,1.6,0,0,0,25.6,42H44.4A1.6,1.6,0,0,0,46,40.5V34.7A1.7,1.7,0,0,0,45.2,33.3ZM42,38H28V36a13.5,13.5,0,0,1,14,0Z"></path>
                    <path d="M35,28a6,6,0,1,0-6-6A6,6,0,0,0,35,28Zm0-8a2,2,0,1,1-2,2A2,2,0,0,1,35,20Z"></path>
                    <path d="M26,28V9H22V4H6V9H2V40a2,2,0,0,0,4,0V13h4V8h8v5h4V28a2,2,0,0,0,4,0Z"></path>
                    <path d="M15,17h2a1,1,0,0,0,0-2H15V13a1,1,0,0,0-2,0v2H11a1,1,0,0,0,0,2h2v2a1,1,0,0,0,2,0Z"></path>
                    <rect x="9" y="22" width="4" height="4"></rect>
                    <rect x="15" y="22" width="4" height="4"></rect>
                    <rect x="9" y="28" width="4" height="4"></rect>
                    <rect x="15" y="28" width="4" height="4"></rect>
                    <rect x="9" y="34" width="4" height="4"></rect>
                    <rect x="15" y="34" width="4" height="4"></rect>
                </g>
            </g>
        </g>
    </g>
</svg>
<svg v-else-if="name === 'supplies'" fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    <g id="SVGRepo_iconCarrier">
        <path d="M20.929,1.629A1,1,0,0,0,20,1H4a1,1,0,0,0-.929.629C1.04,6.778,1.022,6.589,1.008,6.961,1.007,6.975,1,6.986,1,7V22a1,1,0,0,0,1,1H22a1,1,0,0,0,1-1V7C23,6.539,22.5,5.7,20.929,1.629ZM4.677,3H19.323l1.2,3H3.477ZM21,21H3V8H21ZM11,15.5H9v-2h2v-2h2v2h2v2H13v2H11Z"></path>
    </g>
</svg>
<svg v-else-if="name === 'history'" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path d="M12,2A10,10,0,0,0,5.12,4.77V3a1,1,0,0,0-2,0V7.5a1,1,0,0,0,1,1H8.62a1,1,0,0,0,0-2H6.22A8,8,0,1,1,4,12a1,1,0,0,0-2,0A10,10,0,1,0,12,2Zm0,6a1,1,0,0,0-1,1v3a1,1,0,0,0,1,1h2a1,1,0,0,0,0-2H13V9A1,1,0,0,0,12,8Z" fill="#6563ff" /></svg>
<svg v-else-if="name === 'buble'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
    <path fill="#6563ff" d="M273.536 736H800a64 64 0 0 0 64-64V256a64 64 0 0 0-64-64H224a64 64 0 0 0-64 64v570.88zM296 800 147.968 918.4A32 32 0 0 1 96 893.44V256a128 128 0 0 1 128-128h576a128 128 0 0 1 128 128v416a128 128 0 0 1-128 128z"></path>
    <path fill="#6563ff" d="M512 499.2a51.2 51.2 0 1 1 0-102.4 51.2 51.2 0 0 1 0 102.4zm192 0a51.2 51.2 0 1 1 0-102.4 51.2 51.2 0 0 1 0 102.4zm-384 0a51.2 51.2 0 1 1 0-102.4 51.2 51.2 0 0 1 0 102.4z"></path>
</svg>
<svg v-else-if="name === 'profile'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 text-primary">
    <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" />
</svg>
<svg v-else-if="name === 'hamburger'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
</svg>

<svg v-else-if="name === 'home'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
    <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
    <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
</svg>

<svg v-else-if="name === 'usersList'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
    <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" />
</svg>
<svg v-else-if="name === 'departments'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
    <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 0 0 1.5v16.5h-.75a.75.75 0 0 0 0 1.5H15v-18a.75.75 0 0 0 0-1.5H3ZM6.75 19.5v-2.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75ZM6 6.75A.75.75 0 0 1 6.75 6h.75a.75.75 0 0 1 0 1.5h-.75A.75.75 0 0 1 6 6.75ZM6.75 9a.75.75 0 0 0 0 1.5h.75a.75.75 0 0 0 0-1.5h-.75ZM6 12.75a.75.75 0 0 1 .75-.75h.75a.75.75 0 0 1 0 1.5h-.75a.75.75 0 0 1-.75-.75ZM10.5 6a.75.75 0 0 0 0 1.5h.75a.75.75 0 0 0 0-1.5h-.75Zm-.75 3.75A.75.75 0 0 1 10.5 9h.75a.75.75 0 0 1 0 1.5h-.75a.75.75 0 0 1-.75-.75ZM10.5 12a.75.75 0 0 0 0 1.5h.75a.75.75 0 0 0 0-1.5h-.75ZM16.5 6.75v15h5.25a.75.75 0 0 0 0-1.5H21v-12a.75.75 0 0 0 0-1.5h-4.5Zm1.5 4.5a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Zm.75 2.25a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75v-.008a.75.75 0 0 0-.75-.75h-.008ZM18 17.25a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Z" clip-rule="evenodd" />
</svg>
<svg v-else-if="name === 'applications'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
    <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd" />
    <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375ZM6 12a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V12Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM6 15a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V15Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM6 18a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V18Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
</svg>
<svg v-else-if="name === 'settings'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
    <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" clip-rule="evenodd" />
</svg>
<svg v-else-if="name === 'stalls'" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="currentColor">
  <path d="M8 26v30h14V42h20v14h14V26H8zm48-4H8L32 10l24 12z"/>
</svg>
<svg v-else-if="name === 'volante-type'" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="currentColor">
  <path d="M48 4H16a4 4 0 0 0-4 4v48a4 4 0 0 0 4 4h32a4 4 0 0 0 4-4V8a4 4 0 0 0-4-4zM20 16h24v4H20v-4zm0 10h24v4H20v-4zm0 10h16v4H20v-4zm24 14a6 6 0 1 1-6 6 6 6 0 0 1 6-6z"/>
  <path d="M42.5 56.5l1.5 1.5 3-3-1.5-1.5-1.5 1.5-1.5-1.5-1.5 1.5z" fill="#fff"/>
</svg>

<svg v-else-if="name === 'stall-type'" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="currentColor">
  <path d="M10 12v20l22 22 22-22L32 12H10zm22 34L14 28V16h18l18 18-18 18z"/>
  <circle cx="20" cy="20" r="2" fill="#fff"/>
</svg>
<svg v-else-if="name === 'requirements'" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="currentColor">
  <path d="M48 8H16a4 4 0 0 0-4 4v40a4 4 0 0 0 4 4h32a4 4 0 0 0 4-4V12a4 4 0 0 0-4-4zM20 20h24v4H20v-4zm0 10h24v4H20v-4zm0 10h16v4H20v-4z"/>
  <path d="M18 48l2 2 4-4-2-2-2 2-2-2-2 2z" fill="#000"/>
</svg>
<svg v-else-if="name === 'business-permits'" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="currentColor">
  <path d="M32 6L6 20h52L32 6z"/>
  <path d="M12 24v28h8V24h-8zm12 0v28h8V24h-8zm12 0v28h8V24h-8zm12 0v28h8V24h-8z"/>
  <path d="M10 52h44v4H10z"/>
</svg>
<svg v-else-if="name === 'roles'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
  <path d="M3 3h6v6H3V3zm0 12h6v6H3v-6zm12-12h6v6h-6V3zm0 12h6v6h-6v-6zM9 9h6v6H9V9z"/>
</svg>
<svg v-else-if="name === 'admin-type'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
  <path d="M12 1l9 4v6c0 5.55-3.84 10.74-9 12-5.16-1.26-9-6.45-9-12V5l9-4z"/>
</svg>
<svg v-else-if="name === 'sub-admin'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4s-4 1.79-4 4 1.79 4 4 4z" />
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20v-1c0-2.21 3.58-4 8-4s8 1.79 8 4v1" />
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3l5 5m0 0l-5 5m5-5H9" />
</svg>

<svg v-else-if="name === 'user-type'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-6 8v-1c0-2.21 3.58-4 6-4s6 1.79 6 4v1H6z"/>
</svg>
<svg v-else-if="name === 'all-users'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
  <path d="M10 10a4 4 0 100-8 4 4 0 000 8zM2 18a8 8 0 0116 0H2z"/>
</svg>
<svg v-else-if="name === 'inspectors'" xmlns="http://www.w3.org/2000/svg" class='w-4 h-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
    d='M15 17h5l-1.405-1.405M17.595 15.595A4.5 4.5 0 1019.5 12a4.5 4.5 0 00-1.905 3.595zM12 14a5 5 0 100-10 5 5 0 000 10zM12 14c-2.67 0-8 1.34-8 4v2h8'/>
  </svg>
<svg v-else-if="name === 'apply-new'" xmlns="http://www.w3.org/2000/svg" class='w-4 h-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
  <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 3h16a1 1 0 011 1v16a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1zm1 2v14h14V5H5zm3 3h8v2H8V8zm0 4h8v2H8v-2z'/>
</svg>
<svg v-else-if="name === 'fees'"  xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path d="M12 1C5.92487 1 1 5.92487 1 12C1 18.0751 5.92487 23 12 23C18.0751 23 23 18.0751 23 12C23 5.92487 18.0751 1 12 1ZM12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21Z"/>
  <path d="M12 6C8.68629 6 6 8.23858 6 11C6 13.7614 8.68629 16 12 16C15.3137 16 18 13.7614 18 11C18 8.23858 15.3137 6 12 6ZM12 14C9.79086 14 8 12.6569 8 11C8 9.34315 9.79086 8 12 8C14.2091 8 16 9.34315 16 11C16 12.6569 14.2091 14 12 14Z"/>
</svg>
<svg v-else-if="name === 'download'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
    <path d="M7 2 L7 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M4.5 6.5 L7 9 L9.5 6.5" fill="none" stroke="currentColor" stroke-width="1.5" 
            stroke-linecap="round" stroke-linejoin="round"/>
    <rect x="3" y="11" width="8" height="1" rx="0.5" fill="currentColor"/>
</svg>
<svg  v-else-if="name === 'business-names'" xmlns="http://www.w3.org/2000/svg" 
     viewBox="0 0 64 64" 
     width="64" height="64" 
     fill="none" stroke="currentColor" 
     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">
  <!-- Roof -->
  <path d="M8 20 L32 8 L56 20 Z" />
  <!-- Store body -->
  <rect x="12" y="20" width="40" height="32" rx="2" ry="2"/>
  <!-- Door -->
  <rect x="28" y="36" width="8" height="16"/>
  <!-- Windows -->
  <rect x="16" y="28" width="8" height="6"/>
  <rect x="40" y="28" width="8" height="6"/>
</svg>

<svg v-else-if="name === 'stall-names'" xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 64 64"
     width="64" height="64"
     fill="none" stroke="currentColor"
     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">
  <!-- Stall outline -->
  <rect x="12" y="20" width="40" height="28" rx="2" ry="2"/>
  
  <!-- Signboard / nameplate -->
  <rect x="18" y="10" width="28" height="8" rx="1" ry="1"/>
  <line x1="22" y1="14" x2="42" y2="14"/>
  
  <!-- Table legs -->
  <line x1="20" y1="48" x2="20" y2="56"/>
  <line x1="44" y1="48" x2="44" y2="56"/>
</svg>

<svg v-else-if="name === 'business-category'" xmlns="http://www.w3.org/2000/svg" 
     viewBox="0 0 64 64" 
     width="64" height="64" 
     fill="currentColor" stroke="currentColor" 
     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">
  <!-- Canopy -->
  <rect x="8" y="8" width="48" height="12" rx="2" ry="2" />
  <!-- Hanging lines (like stall partitions) -->
  <line x1="16" y1="20" x2="16" y2="36"/>
  <line x1="32" y1="20" x2="32" y2="36"/>
  <line x1="48" y1="20" x2="48" y2="36"/>
  <!-- Table counter -->
  <rect x="12" y="36" width="40" height="12" rx="2" ry="2"/>
  <!-- Legs -->
  <line x1="20" y1="48" x2="20" y2="56"/>
  <line x1="44" y1="48" x2="44" y2="56"/>
</svg>

<svg v-else-if="name === 'business-size'" xmlns="http://www.w3.org/2000/svg" 
     viewBox="0 0 64 64" 
     width="64" height="64" 
     fill="none" stroke="currentColor" 
     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">
  <!-- Stall outline -->
  <rect x="12" y="12" width="40" height="40" rx="2" ry="2"/>
  
  <!-- Width arrow -->
  <line x1="16" y1="54" x2="48" y2="54"/>
  <polyline points="16,50 12,54 16,58"/>
  <polyline points="48,50 52,54 48,58"/>
  
  <!-- Height arrow -->
  <line x1="54" y1="16" x2="54" y2="48"/>
  <polyline points="50,16 54,12 58,16"/>
  <polyline points="50,48 54,52 58,48"/>
</svg>

<svg v-else-if="name === 'business-area'" xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 24 24"
     fill="currentColor"
     width="24" height="24"
     aria-label="Area square meter icon">
  <!-- Box outline -->
  <rect x="3" y="6" width="18" height="12" rx="2" />
  
  <!-- m² text (stylized with paths) -->
  <path d="M7 15v-4l1.5 2 1.5-2v4" /> <!-- 'm' -->
  <path d="M12 11h2v2h-2z" />          <!-- small square for '²' -->
</svg>

<svg v-else-if="name === 'vendor'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-label="Vendors"> 

  <!-- Awning --> 

  <path d="M4 7h16l-1.6-3.2A2 2 0 0 0 16.6 2H7.4A2 2 0 0 0 5.6 3.8L4 7z"/> 

  <!-- Scallops --> 

  <path d="M4 7c0 1.7 1.3 3 3 3s3-1.3 3-3"/> 

  <path d="M10 7c0 1.7 1.3 3 3 3s3-1.3 3-3"/> 

  <path d="M16 7c0 1.7 1.3 3 3 3s3-1.3 3-3"/> 

  <!-- Counter / Stall body --> 

  <rect x="4" y="10" width="16" height="8" rx="1.5"/> 

  <!-- Person badge (vendor) --> 

  <circle cx="9" cy="15" r="1.6"/> 

  <path d="M6.8 18.5a3.4 3.4 0 0 1 4.4 0"/> 

</svg> 
<!-- simple-table.svg -->
<svg v-else-if="name === 'table'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"  aria-hidden="true">
  <rect x="8" y="18" width="48" height="14" rx="2" ry="2" fill="none" stroke="currentColor" stroke-width="3"/>
  <rect x="16" y="32" width="32" height="4" fill="currentColor"/>
  <rect x="12" y="36" width="4" height="14" fill="currentColor"/>
  <rect x="48" y="36" width="4" height="14" fill="currentColor"/>
</svg>

</template>

<script>
export default {
    props: {
        name: String,
    },
}
</script>
