
function openNav() {
    const sidebar = document.querySelector("aside");
    const maxSidebar = document.querySelector(".max")
    const miniSidebar = document.querySelector(".mini")
    const maxToolbar = document.querySelector(".max-toolbar")
    const arrow = document.querySelector('.arrow')
    if(sidebar.classList.contains('-translate-x-48')){
        console.log('open')
        // max sidebar
        arrow.classList.add("rotate-180")
        arrow.classList.remove("hover:rotate-180")
        arrow.classList.add("hover:-rotate-0")
        sidebar.classList.remove("-translate-x-48")
        sidebar.classList.add("translate-x-none")
        maxSidebar.classList.remove("hidden")
        maxSidebar.classList.add("flex")
        miniSidebar.classList.remove("flex")
        miniSidebar.classList.add("hidden")
        maxToolbar.classList.add("translate-x-0")
        maxToolbar.classList.remove("translate-x-24","scale-x-0")
    } else{
        console.log('close')
        // mini sidebar
        arrow.classList.remove("rotate-180")
        arrow.classList.add("hover:rotate-180")
        arrow.classList.remove("hover:-rotate-0")
        sidebar.classList.add("-translate-x-48")
        sidebar.classList.remove("translate-x-none")
        maxSidebar.classList.add("hidden")
        maxSidebar.classList.remove("flex")
        miniSidebar.classList.add("flex")
        miniSidebar.classList.remove("hidden")
        maxToolbar.classList.add("translate-x-24","scale-x-0")
        maxToolbar.classList.remove("translate-x-0")
    }
}