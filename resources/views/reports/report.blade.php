<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير الشكاوي والعمليات</title>

    <style>
        body {
            font-family: dejavusans;
            direction: rtl;
            font-size: 11px;
        }

        h2, h3 {
            text-align: center;
            font-weight: bold;
            margin: 6px 0;
        }
    </style>
</head>

<body>

<!-- ================= العنوان ================= -->
<h2>تقرير الشكاوي والعمليات</h2>

<!-- ================= الفترة ================= -->
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" style="line-height:20px;">
  إلى <b>{{ $end_date }}</b>   <b>{{ $start_date }}</b> من الفترة  
        </td>
    </tr>
</table>

<br>

<!-- ================= جدول الشكاوى ================= -->
<h3>الشكاوي (عدد: {{ $complaints_count }})</h3>

<table width="100%" border="1" cellpadding="6" cellspacing="0">

    <tr bgcolor="#d6eaf8">

        <th width="15%">
            <table width="100%"><tr><td align="center">الموقع</td></tr></table>
        </th>

        <th width="25%">
            <table width="100%"><tr><td align="center">الوصف</td></tr></table>
        </th>

        <th width="20%">
            <table width="100%"><tr><td align="center">نوع الشكوى</td></tr></table>
        </th>

        <th width="15%">
            <table width="100%"><tr><td align="center">رقم الشكوى</td></tr></table>
        </th>

        <th width="10%">
            <table width="100%"><tr><td align="center">الحالة</td></tr></table>
        </th>

        <th width="15%">
            <table width="100%"><tr><td align="center">تاريخ الإنشاء</td></tr></table>
        </th>

    </tr>

    @foreach($complaints as $i => $c)
    <tr bgcolor="{{ $i % 2 == 0 ? '#f9fbfd' : '#ffffff' }}">

        <td>
            <table width="100%"><tr><td align="center">{{ $c['location'] }}</td></tr></table>
        </td>

        <td align="right" style="line-height:18px;">
            {!! nl2br(e($c['description'])) !!}
        </td>

        <td>
            <table width="100%"><tr><td align="center">{{ $c['complaint_type'] }}</td></tr></table>
        </td>

        <td>
            <table width="100%"><tr><td align="center">{{ $c['complaint_number'] }}</td></tr></table>
        </td>

        <td>
            <table width="100%"><tr><td align="center">{{ $c['status'] }}</td></tr></table>
        </td>

        <td>
            <table width="100%"><tr>
                <td align="center">
                    {{ \Carbon\Carbon::parse($c['created_at'])->format('Y-m-d') }}
                </td>
            </tr></table>
        </td>

    </tr>
    @endforeach
</table>

<br><br>

<!-- ================= جدول العمليات ================= -->
<h3>عمليات الموظفين (عدد: {{ $operations_count }})</h3>

<table width="100%" border="1" cellpadding="6" cellspacing="0">

    <tr bgcolor="#fdebd0">
        <th width="80%">
            <table width="100%"><tr><td align="center">التفاصيل</td></tr></table>
        </th>
        <th width="20%">
            <table width="100%"><tr><td align="center">تاريخ العملية</td></tr></table>
        </th>
    </tr>

    @foreach($operations as $i => $op)
    <tr bgcolor="{{ $i % 2 == 0 ? '#fffaf2' : '#ffffff' }}">

        <td align="right" style="line-height:18px;">
            {!! nl2br(e($op['details'])) !!}
        </td>

        <td>
            <table width="100%"><tr><td align="center">{{ $op['operation_date'] }}</td></tr></table>
        </td>

    </tr>
    @endforeach
</table>

</body>
</html>
