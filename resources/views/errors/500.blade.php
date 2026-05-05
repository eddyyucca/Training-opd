@include('errors.layout', [
    'code'     => '500',
    'title'    => 'Server Error',
    'subtitle' => 'Something went wrong on our end. Please try again in a moment.',
    'detail'   => 'If the problem persists, contact your system administrator and provide the time this error occurred.',
])
