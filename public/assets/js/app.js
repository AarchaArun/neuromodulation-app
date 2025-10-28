// app.js — handles auto age and total score calculations

$(document).ready(function () {

    // Auto-calculate Age from DOB
    $('#dob').on('change', function () {
        const dob = new Date($(this).val());
        if (isNaN(dob)) return;
        const diffMs = Date.now() - dob.getTime();
        const ageDate = new Date(diffMs);
        const age = Math.abs(ageDate.getUTCFullYear() - 1970);
        $('#age').val(age);
    });

    // Auto-calculate total score (questions 2–12)
    $('input.score-input').on('input', function () {
        let total = 0;
        for (let i = 2; i <= 12; i++) {
            const val = parseInt($(`#q${i}`).val()) || 0;
            total += val;
        }
        $('#totalScore').val(total);
    });

});
