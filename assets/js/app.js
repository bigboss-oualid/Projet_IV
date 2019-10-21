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

import '../../node_modules/@bower_components/amsul/lib/picker.js'
import '../../node_modules/@bower_components/amsul/lib/picker.date'


console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
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

//add FR Langauge
$.extend( $.fn.pickadate.defaults, {
    monthsFull: [ 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ],
    monthsShort: [ 'Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec' ],
    weekdaysFull: [ 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi' ],
    weekdaysShort: [ 'Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam' ],
    today: 'Aujourd\'hui',
    clear: 'Effacer',
    close: 'Fermer',
    firstDay: 1,
    labelMonthNext:"Mois suivant",
    labelMonthPrev:"Mois précédent",
    labelMonthSelect:"Sélectionner un mois",
    labelYearSelect:"Sélectionner une année"
});


var currentYear = new Date().getFullYear();

$('div > [data-toggle="datepicker-visit"]').on('click',function(){
    $(this).pickadate({
        format: 'ddd d mmmm, yyyy',
        formatSubmit: 'dd-mm-yyyy',
        min: true,
        selectMonths: true,
        selectYears: 2, //true
        disable: [
            // 0 => january
                [currentYear,4,1],[currentYear,10,1],[currentYear,11,25],
                2,
            ],
    });
});


$(document).on('click',function(){
    $('input[id$=_birthday]').pickadate({
        format: 'ddd d mmmm, yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max: true,
        selectMonths: true,
        selectYears: 100, //true
    });
});



/*
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
*/