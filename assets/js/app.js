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

    //get error-data from visitors.html.twig;
    let errors = $("div[error-data]").attr('error-data');

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

//remove select option full-day if user select today as visit date
function checkSelectedDay(context) {
    /*
     * get local DateTime, selected Date & split them to [dd/mm/yyy] [à] [(H):(i):(s)]
     */
    let dateNow = new Date();
    let dateLocal= dateNow.toLocaleString('fr-Fr');
    let dividedDay = dateLocal.split(' ');
    let selectedDate = new Date(context.select).toLocaleString('fr-Fr');
    let SelectedSplittedDate = selectedDate.split(' ');
    let time = dividedDay[2].split(':');

    let selectTiTyElt = document.getElementById('order_fullDay');
    let choiceOptionElt = selectTiTyElt.children[0];

    if (dividedDay[0] === SelectedSplittedDate[0] && Number(time[0]) > 13 ){
        choiceOptionElt.style.display = 'none';
        selectTiTyElt.value = selectTiTyElt.children[1].value;
    } else {
        choiceOptionElt.style.display = 'block';
        selectTiTyElt.value = selectTiTyElt.children[0].value;
    }
}

let currentYear = (new Date().getFullYear());
let holidays = []
// month start with 0 index (2019/4/1 =>2019/mai/1)
for (let i = 0; i< 3; i++){
    holidays.push([currentYear+i,4,1],[currentYear+i,10,1],[currentYear+i,11,25]);
}

$('div > [data-toggle="datepicker-visit"]').on('click',function(){
    let $input = $(this).pickadate({
        format: 'ddd d mmmm yyyy',
        formatSubmit: 'dd/mm/yyyy',
        min: true,
        selectMonths: true,
        selectYears: 2, //true default to 10
        disable: [
                2,
            ],
        onSet: function(context) {
            checkSelectedDay(context);
        }
    });
    let picker = $input.pickadate('picker');
    picker.set('disable',holidays)
});

$(document).on('click',function(){
    $('input[id$=_birthday]').pickadate({
        format: 'ddd d mmmm, yyyy',
        formatSubmit: 'dd/mm/yyyy',
        max: true,
        selectMonths: true,
        selectYears: 100, //true
    });
});
