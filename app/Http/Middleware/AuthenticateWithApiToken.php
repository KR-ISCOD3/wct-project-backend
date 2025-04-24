<?php
  namespace App\Http\Middleware;

  use Closure;
  use Illuminate\Http\Request;
  use App\Models\User;

  class AuthenticateWithApiToken
  {
      public function handle(Request $request, Closure $next)
      {
          $token = $request->bearerToken();

          if ($token) {
              // Find the user by the provided token
              $user = User::where('api_token', $token)->first();

              if ($user) {
                  // Authenticate the user
                  auth()->login($user);
              }
          }

          return $next($request);
      }
  }
