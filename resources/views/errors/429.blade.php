@extends('errors.layout')

@section('code', '429')
@section('message', $exception->getMessage() ?: __('Too Many Requests.'))

