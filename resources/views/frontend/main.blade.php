@extends('frontend.layouts.app')

@section('title', 'GYM - Transform Your Body & Mind')

@section('content')
    @include('frontend.sections.hero', ['stats' => $stats ?? []])
    @include('frontend.sections.about')
    @include('frontend.sections.stats', ['stats' => $stats ?? []])
    @include('frontend.sections.services')
    @include('frontend.sections.membership')
    @include('frontend.sections.classes', ['gymClasses' => $gymClasses ?? collect()])
    @include('frontend.sections.trainers', ['trainers' => $trainers ?? collect()])
    @include('frontend.sections.testimonials')
    @include('frontend.sections.contact')
@endsection

@push('scripts')
<script>
    // Show success/error messages
    @if(session('success'))
        // You can integrate with your preferred notification library here
        alert('{{ session('success') }}');
    @endif

    @if(session('error'))
        alert('{{ session('error') }}');
    @endif
</script>
@endpush
