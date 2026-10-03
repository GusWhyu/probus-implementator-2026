<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:px-40">
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <div class="bg-white rounded-[28px] shadow-md card">
                            <a href="{{ url()->previous() }}" class="text-5xl text-black mt-2 ml-2 hover:text-gray-700"><i class="fa-solid fa-circle-arrow-left"></i></a>
                            <div class="card-body">
                                <h1 class="card-title text-3xl font-extrabold">Change Account Password</h1>
                                <form action="/editpassacc" method="POST">
                                    @csrf
                                    <div class="mt-3">
                                        <p class="font-bold">Current Password</p>
                                        <input type="password" class="input rounded-xl input-bordered w-full" name="current_password" id="current_password">
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-bold">New Password</p>
                                        <input type="password" class="input rounded-xl input-bordered w-full" name="new_password" id="new_password">
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-bold">Confirm New Password</p>
                                        <input type="password" class="input rounded-xl input-bordered w-full" name="new_password_confirmation" id="new_password_confirmation">
                                        <button type="button" onclick="toggleAllPasswords()" class="mt-2">Show Password <i class="fa-solid fa-eye"></i></button>
                                    </div>      
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-neutral bg-black w-full">Change Password</button>
                                    </div>
                                </form>
                                <script>
                                    function toggleAllPasswords() {
                                        var passwordFields = document.querySelectorAll('input[type="password"], input[type="text"]');
                                        passwordFields.forEach(function(field) {
                                            if (field.type === "password") {
                                                field.type = "text";
                                            } else {
                                                field.type = "password";
                                            }
                                        });
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>