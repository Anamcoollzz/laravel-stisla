@extends('errors.layout')

@section('code', '401')
@section('message', $exception->getMessage() ?: __('You are not authorized to view this page.'))

