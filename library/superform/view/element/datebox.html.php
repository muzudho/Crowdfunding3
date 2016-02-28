<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */
?>
<input name="<?php echo htmlspecialchars($this['name']) ?>" type="text"<?php if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"'?>  value="<?php if (isset($this['value'])) echo htmlspecialchars($this['value']) ?>"<?php if (isset($this['size'])) echo 'size="' . ((int) $this['size']) . '"' ?> />
<script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/datepicker.min.js"></script>
<script type="text/javascript">
    
(function($){ 
    $(function(){
        var li= $('li.element#<?php echo $this['id'] ?>');
        var input = li.children('div.contents').find('input');
        if (input.length) {
            var lastVal = input.val();
            var updating = null;

            var update = function () {
                var val = input.val();
                clearTimeout(updating);
                if (val != lastVal) {
                    lastVal = val;
                    li.addClass('busy');
                    updating = setTimeout(function () {   
                        window.Superform.update(input, function () {
                            li.removeClass('busy');
                        });
                    });  
                } else {           
                    li.removeClass('busy');
                }
            };

            input.change(function () { 
                update();          
            });
        }  

        var dp = $('#<?php echo $this['id'] ?> input');

        dp.DatePicker({           
            format: 'Y-m-d',
            date: '<?php echo $this['value'] ?>',
            current: '<?php echo $this['value'] ?>',
            starts: 1,
            position: 'bottom',      
            eventName: 'click',
            onBeforeShow: function(){
                dp.DatePickerSetDate(dp.val(), true);                
            },
            onChange: function(formatted, dates){        
                    dp.val(formatted);
                    dp.DatePickerHide();
                    dp.focus();
                    update();
            },
            /*locale: {
                days: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábad', 'Domingo'],
                daysShort: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                daysMin: ['L', 'M', 'X', 'J', 'V', 'S', 'D'],
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                week: []
            }*/
            locale: {
                days: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
                daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                daysMin: ['日', '月', '火', '水', '木', '金', '土'],
                months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                week: []
            }
        }); 
    });         
})(jQuery);
</script>

