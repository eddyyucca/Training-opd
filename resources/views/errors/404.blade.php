@include('errors.layout', [
    'code'     => '404',
    'title'    => 'Page Not Found',
    'subtitle' => 'The page or resource you are looking for does not exist or has been removed.',
    'detail'   => 'Double-check the URL, or use the buttons below to navigate back to a safe page.',
])
