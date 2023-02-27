<form action="{{ route('editProfileAction') }}" method="POST">
  @csrf
  @method('PUT')
  <div class="form-group">
    <label for="firstName">First Name:</label>
    <input type="text" class="form-control" id="firstName" name="firstName" value="{{ $user['firstName'] }}" />
  </div>
  <div>
    <label for="lastName">Last Name:</label>
    <input type="text" class="form-control" id="lastName" name="lastName" value="{{ $user['lastName'] }}" />
  </div>
  <div>
    <label for="email">Email:</label>
    <input type="email" class="form-control" id="email" name="email" value="{{ $user['email']}}" />
  </div>
  <div>
    <label for="password">Password:</label>
    <input type="password" class="form-control" id="password" name="password" />
  </div>
  <div>
    <label for="password_confirmation">Confirm Password:</label>
    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" />
  </div>
  <div>
    <button type="submit">Update Profile</button>
  </div>
</form>
