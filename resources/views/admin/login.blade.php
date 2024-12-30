<x-admin.layout>

<div class="mx-auto max-w-screen-xl px-4 py-16 sm:px-6 lg:px-8">
  <div class="mx-auto max-w-lg text-center">
    <h1 class="text-2xl font-bold sm:text-3xl">Admin Sign in</h1>

    <p class="mt-4 text-gray-500">
        This is the login page for the Gym Diary administrator site.
    </p>
  </div>

  <form action="{{ route('admin.login.store') }}" method="POST" class="mx-auto mb-0 mt-8 max-w-md space-y-4">
    @csrf
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Password')" />

        <x-text-input id="password" class="block mt-1 w-full"
            type="password"
            name="password"
            required autocomplete="current-password" />
      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="flex items-center justify-between">

      <button
        type="submit"
        class="inline-block rounded-lg bg-blue-500 mt-7 px-5 py-3 text-sm font-medium text-white w-full"
      >
        Sign in
      </button>
    </div>
  </form>
</div>
</x-admin.layout>