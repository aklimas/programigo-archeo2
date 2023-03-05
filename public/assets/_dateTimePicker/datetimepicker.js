$('.form_date').datetimepicker({
    language: 'pl',
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0,
    format: 'yyyy-mm-dd',
});

$('.form_time').datetimepicker({
    language: 'pl',
    pickDate: false,
    minuteStep: 5,
    pickerPosition: 'bottom-right',
    format: 'HH:ii',
    autoclose: true,
    showMeridian: true,
    startView: 1,
    maxView: 1,
});

$('.datetimepicker .datetimepicker-hours .table-condensed').each(function(){
    $(this).css('width','190px');
    $(this).find('thead').hide();
})
$('.datetimepicker .datetimepicker-minutes .table-condensed').each(function(){
    $(this).css('width','190px');
    $(this).find('thead').hide();
})