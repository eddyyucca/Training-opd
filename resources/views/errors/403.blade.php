@include('errors.layout', [
    'code'     => '403',
    'title'    => 'Access Denied',
    'subtitle' => 'You do not have permission to access this page or resource.',
    'detail'   => $exception?->getMessage() ?: 'Your account role does not have access to this section. Contact your OPD administrator if you believe this is a mistake.',
])
