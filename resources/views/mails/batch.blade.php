<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Smart Marina</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
    </style>

</head>
<body>
<header>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
               <h1>Alqattan Marrine<br></h1>
            </div>
            <div class="col-md-4">
                <img src="{{asset('images/qattan.jpeg')}}" alt="contract">
            </div>
            <div class="col-md-4">
                <p dir="rtl">
                  <h1>  مرسى قرية القطان</br></h1>
                </p>
            </div>
        </div>
    </div>

</header>

<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <h2>  سند قبض</h2>
    </div>
    <div class="col-md-4"></div>
</div>
<div class="row">
    <div class="col-md-5"></div>
    <div class="col-md-6" dir="rtl">
        @foreach($contractBatch  as $batch)
            <p dir="rtl">
                التاريخ {{$batch->created_at}} <br>
                استلمنا من السيد / {{$batch->name}}<br>
                مبلغ وقدر/{{$batch->amount}} ريال فقط<br>
                وذألك مقابل دفعه من الايجار الواسطة البحرية و المسماة  ({{$batch->wastaname}})رقم({{$batch->codewasta}})<br>
                **الدفعه من ({{$batch->from}}) الى ( {{$batch->to}})<br>
                التوقيع/

            </p>
        @endforeach
    </div>
    <div class="col-md-1"></div>
</div>


<footer class="container">
    <h4>المملكة العربية السعودية – الشعيبة - شركة المراسم المكية – مرسى قرية القطان – جوال 0597630207 <a> alqattan.marrine@gmail.com</a>
    </h4>
</footer>
</body>
</html>