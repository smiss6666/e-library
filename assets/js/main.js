import $ from 'jquery';
import Popper from 'popper.js';
import loadjs from 'loadjs';

// import Bootstrap from 'bootstrap';
// import { Tooltip  } from 'bootstrap';

//const $ = require('jquery');

// window.jQuery = $;
//require('popper.js');
//require('bootstrap');
// require('components/sidebars');
//require('components/sidebars')



//require('bootstrap');

//import 'bootstrap/dist/js/bootstrap.bundle.min';
// import './components/sidebars';
import 'bootstrap';

//loadjs('//use.fontawesome.com/releases/v5.0.13/js/solid.js');
//loadjs('//use.fontawesome.com/releases/v5.0.13/js/fontawesome.js');


$(document).ready(function () {
//     $('#sidebarCollapse').on('click', function () {
//         $('#sidebar').toggleClass('active');
//     });

    $('#sidebar').find('a[data-opened="opened"]').trigger('click');
});


// <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js"
//         integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ"
//         crossOrigin="anonymous"></script>
// <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js"
//         integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY"
//         crossOrigin="anonymous"></script>
