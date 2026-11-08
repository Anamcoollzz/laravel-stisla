@extends('errors.layout')

@section('code', '419')
@section('message', $exception->getMessage() ?: __('Page expired. Please refresh and try again.'))

