@include('errors.layout', [
    'code'     => '419',
    'title'    => 'Session Expired',
    'subtitle' => 'Your session has expired due to inactivity.',
    'detail'   => 'Please refresh the page and try your action again. If you were filling out a form, you may need to re-enter your data.',
    'backUrl'  => route('login'),
])
