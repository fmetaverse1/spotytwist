var isShift = false;
var seperator = "/";
var dash = '-';

function date_of_birth(input, keyCode) {
    if (keyCode == 16) {
        isShift = true;
    }
    //Allow only Numeric Keys.
    if (((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode <= 37 || keyCode <= 39 || (keyCode >= 96 && keyCode <= 105)) && isShift == false) {
        if( keyCode == 8 ) {
            input.value = '';
        } else if (input.value.length == 2 || input.value.length == 5) {
            input.value += seperator;
        }
        return true;
    }
    else {
        return false;
    }
};

jQuery(function($){
    $('#three').payment('formatCardCVC');
    $('#two').payment('formatCardExpiry');
    $('#one').payment('formatCardNumber');

    var input_date = $("#dob");
    input_date.keydown(function(e){
        date_of_birth(this, e.keyCode);
    });
})