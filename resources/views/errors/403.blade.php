@extends('errors.layout')

@section('code', '403')
@section('message', $exception->getMessage() ?: __('You do not have access to this page.'))

