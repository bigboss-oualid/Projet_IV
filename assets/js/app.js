/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
let $ = require('jquery');
require('bootstrap');
import '../../node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min';
import '../../node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.min';
//import '../../node_modules/gijgo/js/gijgo.min';
//import '../../node_modules/gijgo/js/messages/messages.fr-fr';
//import '../../node_modules/@chenfengyuan/datepicker/dist/datepicker';


/* home-page display infos about tarifs */
$('span').ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});

//add Visitors Forms CollectionType
$(document).ready(function() {
    //Get CollectionType form
    let $container = $('div#order_visitors');
    //get visitors number from attr send from VisitorsType.php
    let ticketNbr = Number($("div[data-visitors-nbr]").attr('data-visitors-nbr'));

    let errors = '';//'{{ form.vars.errors.form.getErrors(true) }}';

    let i;
    for (i = 0; i < ticketNbr; i++)
    {
        if(errors === '')
        {
            addVisitorForm($container);
        }
        else
        {
            break;
        }
    }
    function addVisitorForm($container) {
        //Replace value of <legend> & modify input's attributes (id,name) in « data-prototype » in order to generate different fields with different name and ID
        let template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Billet n°' + (i+1))
            .replace(/__name__/g,        i)
        ;
        let $prototype = $(template);

        $container.append($prototype);
        //$container.append('<hr>');
    }
});

$('div > #order_visitedFor').ready(function(){
    $('div > [data-toggle="datepicker-visit"]').datepicker({
        language: 'fr-FR',
        format: 'dd/mm/yyyy',
        autoclose: true,
        clearBtn: true,
        orientation: 'auto',
        startDate: '+0d',
        title: "Date de visite",
        todayBtn: true,
        todayHighlight: true,
        daysOfWeekDisabled: [2],
        container: $(document.activeElement).parent(),
        endDate:  '+1y',
        //daysOfWeekHighlighted: [0,6],
        //datesDisabled: [], Array of date strings or a single date string formatted in the given date format

    });
});

$('document').ready(function(){
    $("input[id$=_birthday]").each( function () {
        $(this).datepicker({
            language: 'fr-FR',
            format: 'dd/mm/yyyy',
            autoclose: true,
            clearBtn: true,
            orientation: 'bottom',
            endDate: '+1d',
            title: "Date de naissance",
            todayBtn: true,
            todayHighlight: true,
            container: $("input[id$=_birthday]").offsetParent(),
        });
    });
});

/*
    $(document).on('focus', "div", function() {
    $(this).datepicker({
        language: 'fr-FR',
        format: 'dd/mm/yyyy',
        autoclose: true,
        clearBtn: true,
        orientation: 'auto',
        startDate: '+0d',
        title: "Date de visite",
        todayBtn: true,
        todayHighlight: true,
        container: $(document.activeElement).parent(),
        forceParse: false
        //endDate:  '+1y',
        //daysOfWeekHighlighted: [0,6],
        //container: '#order_reservedFor',
        //daysOfWeekDisabled: 2,
        //datesDisabled: [], Array of date strings or a single date string formatted in the given date format

    });
});

    let now = new Date();
    let today = (function() {
        let day = ("0" + now.getDate()).slice(-2);
        let month = ("0" + now.getMonth()).slice(-2);
        let year = now.getFullYear();

        return day + '/' + month + '/' + year;
    })();

//minDate und maxDate
let now = new Date();
let yesterday = function() {
    date.setDate(date.getDate()-1);
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
};
let tomorrow = (function() {
    now.setDate(now.getDate()+1);
    return new Date(now.getFullYear(), now.getMonth(), now.getDate());
})();

/*disableDates:  function (date) {
    var disabled = [10,15,20,25];
    if (disabled.indexOf(date.getDate()) == -1 ) {
        return true;
    } else {
        return false;
    }
}
let now = new Date();
    let today = (function() {
        let day = ("0" + now.getDate()).slice(-2);
        let month = ("0" + now.getMonth()).slice(-2);
        let year = now.getFullYear();

        return day + '/' + month + '/' + year;
    })();
*/





console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
