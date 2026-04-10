@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |  ارسل الخبر

@endsection
@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
@endsection

<div>
    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">أرسل خبر </h4>

                    <ul class="list-breadcrumb">
                        <li>
                            الرئيسية
                        </li>
                        <li>
                            <span>|</span>
                        </li>
                        <li class="active">
                            <a href="#">أرسل خبر </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <div class="container my-5">
        <form wire:submit.prevent="createSendNews" class="border p-4 rounded">
            <div class="row mb-3">
                <div class="col-md-6 mb-xs-2">
                    <label for="fullName" class="form-label">اسم كامل</label>
                    <input  wire:model="state.full_name" type="text" class="form-control" id="fullName" placeholder="الاسم">
                    @error('full_name')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">بريدك الإلكتروني</label>
                    <input  wire:model="state.email" type="email" class="form-control" id="email" placeholder="البريد الإلكتروني">
                    @error('email')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="subject" class="form-label">عنوان الخبر</label>
                <input wire:model="state.subject" type="text" class="form-control" id="subject" placeholder="عنوان">
                @error('subject')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">نص الخبر</label>

                <textarea wire:model="state.message"  class="form-control" id="message" rows="10" placeholder="اكتب..."></textarea>
                @error('message')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary d-block m-auto">إرسال</button>
        </form>
    </div>

</div>
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_contact_form', function () {
                toastr.success('تم إرسال البيانات بنجاح', 'نجاح');


            });
        });
    </script>
@endsection
