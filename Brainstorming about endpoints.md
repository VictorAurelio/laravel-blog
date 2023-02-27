Brainstorming about endpoints

* - Doesn't need to be loggged

(POST)*api/auth/login
(POST)api/auth/logout
(POST)api/auth/refresh

(GET)api/user
(GET)*api/user/{id}
(POST)*api/user/create
(PUT)api/user/edit
(POST)api/user/avatar

*(GET)api/home - posts index
*api/post/{id}
api/post/{id}/edit
api/post/{id}/image
api/post/create

*(GET)api/post/{id}/comment
(POST)api/post/{id}/comment -> comment and like

/401 - not authorized -> redirects to login
