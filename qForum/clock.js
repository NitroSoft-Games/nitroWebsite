"use strict";

function clock_update(){
    // create a new date object
    date = new Date();

    // get hours/minutes/seconds variables from date object
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();

    // if the value is a one-digit number then add a zero in prefix
    if(hours < 10) hours = '0' + hours;
    if(minutes < 10) minutes = '0' + minutes;
    if(seconds < 10) seconds = '0' + seconds;

    // catch the dom element to a variable
    var clock = document.getElementById("clock");

    // write the date format into the variable
    clock.innerHTML = hours + ":" + minutes + ":" + seconds;

    // repeat the selected function with the given timeout in miliseconds
    setTimeout("clock_update()", 1000);
}