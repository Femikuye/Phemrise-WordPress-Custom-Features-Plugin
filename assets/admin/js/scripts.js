window.addEventListener("load", function(){
    // Store the tabs variables
    let tabs = document.querySelectorAll("ul.pwpcf-nav-tabs > li");
    for(let i = 0; i < tabs.length; i++){
        let tab = tabs[i];
        tab.addEventListener("click", switchTab);
    }


    function switchTab(event){
        event.preventDefault();
        document.querySelector("ul.pwpcf-nav-tabs li.active").classList.remove("active");
        document.querySelector(".pwpcf-tab-content .pwpcf-tab-pane.active").classList.remove("active");
        let clickedTab = event.currentTarget;
        let anchor = event.target;
        let activePaneID = anchor.getAttribute("href");
        clickedTab.classList.add("active");
        document.querySelector(activePaneID).classList.add("active");
    }
});


jQuery(document).ready(function($){
    console.log("Helloo jQuery");
    
});
