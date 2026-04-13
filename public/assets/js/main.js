document.addEventListener('DOMContentLoaded', () => {
    
    // sidebar open
    document.querySelector('#sidebar-opener').addEventListener('click', () => {

        // open sidebar
        document.querySelector('#sidebar').classList.add('active');

        // show sidebar overlay
        document.querySelector('#sidebar-overlay').classList.remove('d-none');
    });


    // sidebar close
    document.querySelector('#sidebar-overlay').addEventListener('click', () => {

        // open sidebar
        document.querySelector('#sidebar').classList.remove('active');

        // show sidebar overlay
        document.querySelector('#sidebar-overlay').classList.add('d-none');
    });

});