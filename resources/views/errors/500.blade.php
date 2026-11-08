@extends('errors.layout')

@section('code', '500')
@section('message', $exception->getMessage() ?: __('Whoopps, something went wrong.'))

