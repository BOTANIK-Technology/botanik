<script>
    let currentMonth = '{{ $current_month }}';
    let nextMonth = '{{ $next_month }}';
    let prevMonth = '{{ $prev_month }}';
</script>
<div class="flex direction-column">
    <table id="user_calendar" class="user_calendar border">
    </table>
</div>
@section('modal-styles')
    <style>
        .pointer {
            cursor: pointer
        }
    </style>
@endsection

