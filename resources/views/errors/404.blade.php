@extends('errors.layout')

@section('code', '404')
@section('message', $exception->getMessage() ?: __('The page you were looking for could not be found.'))

