@extends('errors::minimal')


@section('title', $exception->getMessage() ? $exception->getMessage() : __('Not Found'))
@section('code', '404')
@section('message', $exception->getMessage() ? $exception->getMessage() : __('Not Found'))
