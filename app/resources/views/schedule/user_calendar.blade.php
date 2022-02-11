<script>
    let currentMonth = '{{ $current_month }}';
    let nextMonth = '{{ $next_month }}';
    let prevMonth = '{{ $prev_month }}';
</script>
<div class="flex direction-column width-full">
    <table id="user_calendar" class="user_calendar new_style">
    </table>
</div>

<style>
    .create {
        margin-bottom: 20px;
    }

    .create:last-child {
        margin-bottom: 0;
    }

    .width-full {
        width: 100%;
    }

    .user_calendar.new_style {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        width: 100%;
        border: 1px solid #CEB5CE;
        background: #fff;
        -webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-bottom: none;
    }

    .user_calendar.new_style .calendar_row {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: stretch;
        -ms-flex-align: stretch;
        align-items: stretch;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        width: 100%;
        border-bottom: 1px solid #CEB5CE;
    }

    .user_calendar.new_style .calendar_row:first-child {
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        background: #FCF1EF;
        border-color: #CEB5CE;
    }

    .user_calendar.new_style .calendar_row:first-child .calendar_cell {
        -webkit-box-flex: 0;
        -ms-flex-positive: 0;
        flex-grow: 0;
        width: auto;
        min-width: 30px;
        font-weight: bold;
        font-size: 14px;
        border: none;
    }

    .user_calendar.new_style .calendar_row:empty {
        border-bottom: none;
    }

    .user_calendar.new_style .calendar_cell {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-flex: 1;
        -ms-flex-positive: 1;
        flex-grow: 1;
        -ms-flex-negative: 0;
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        font-weight: 500;
        font-size: 12px;
        border-right: 1px solid #CEB5CE;
        padding: 0;
        cursor: pointer;
        position: relative;
    }

    .user_calendar.new_style .calendar_cell:last-child {
        border-right: none;
    }

    .user_calendar.new_style .calendar_cell.month_prev,
    .user_calendar.new_style .calendar_cell.month_next {
        margin: 0 10px;
    }

    .user_calendar.new_style .calendar_cell.month_prev:after,
    .user_calendar.new_style .calendar_cell.month_next:after {
        content: "";
        display: block;
        width: 7px;
        height: 12px;
    }

    .user_calendar.new_style .calendar_cell.month_prev:after {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg width='7' height='12' viewBox='0 0 7 12' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M6 11L1 6L6 1' stroke='%234F4F4F' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3e%3c/path%3e%3c/svg%3e");
    }

    .user_calendar.new_style .calendar_cell.month_next:after {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg width='7' height='12' viewBox='0 0 7 12' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M1 1L6 6L1 11' stroke='%234F4F4F' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3e%3c/path%3e%3c/svg%3e");
    }

    .user_times {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
        -webkit-box-pack: start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        border-color: #CEB5CE;
        max-width: 242px;
    }

    .user_times .time_cell {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        width: 60px;
        min-height: 26px;
        font-size: 12px;
        border-bottom: 1px solid #CEB5CE;
        border-right: 1px solid #CEB5CE;
        padding: 0 20px;
    }

    .user_times .time_cell:nth-last-child(1),
    .user_times .time_cell:nth-last-child(2),
    .user_times .time_cell:nth-last-child(3),
    .user_times .time_cell:nth-last-child(4) {
        border-bottom: none;
    }

    .user_times .time_cell:nth-child(4n) {
        border-right: none;
    }

    .user_times .time_cell:nth-child(4n):nth-last-child(4) {
        border-bottom: 1px solid #CEB5CE;
    }
    .pay-block {
        border: 1px solid #CEB5CE;
        margin: 2px;
    }
    label > input[type=radio]:checked{
        background-color: #1d68a7;
    }

</style>

