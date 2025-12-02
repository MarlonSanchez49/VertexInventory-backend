<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Maneja el registro de nuevos usuarios.
     * * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {

            // VALIDACIÓN CON MENSAJES PERSONALIZADOS
            $validator = Validator::make($request->all(), [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:4'],
                'role_id'  => ['required', 'exists:roles,id'], // Campo role_id
            ], [
                // Mensajes personalizados
                'name.required'     => 'El nombre es obligatorio.',
                'email.required'    => 'El correo electrónico es obligatorio.',
                'email.email'       => 'El correo electrónico no es válido.',
                'email.unique'      => 'Este correo ya está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min'      => 'La contraseña debe tener al menos 4 caracteres.',
                'role_id.required'  => 'El campo role es obligatorio.',
                'role_id.exists'    => 'El rol seleccionado no es válido.',
            ]);

            // SI LA VALIDACIÓN FALLA
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors(), // Lista exacta de errores por cada campo
                    'error_type' => 'VALIDATION_ERROR'
                ], 422);
            }

            // CREAR EL USUARIO
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $request->role_id,
            ]);

            // Cargar la relación de rol para incluirla en la respuesta
            $user->load('role');

            // GENERAR TOKEN
            $token = $user->createToken('auth_token')->plainTextToken;

            // RESPUESTA EXITOSA
            return response()->json([
                'message'     => 'Registro exitoso',
                'user'        => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id'   => $user->role_id,
                    'role_name' => $user->role ? $user->role->name : null,
                ],
                'token'       => $token,
                'token_type'  => 'Bearer',
                'status'      => 'success'
            ], 201);
        } catch (\Throwable $e) {

            // LOG opcional para debug:
            // \Log::error($e);

            return response()->json([
                'message'     => 'Error interno del servidor',
                'error'       => $e->getMessage(),
                'error_type'  => 'SERVER_ERROR'
            ], 500);
        }
    }


    //-----------------------------------------------------------------------------------

    /**
     * Maneja el inicio de sesión del usuario y la generación del token.
     * * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // 1. Validar las credenciales
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $credentials = $request->only('email', 'password');

            // 2. Intentar autenticar al usuario
            if (!Auth::attempt($credentials)) {
                // Si la autenticación falla (email o password incorrectos)
                return response()->json([
                    'message' => 'Credenciales no válidas. Email o contraseña incorrectos.',
                ], 401); // Código 401 Unauthorized
            }

            // 3. Autenticación exitosa. Obtener el usuario autenticado.
            // Esto es seguro porque Auth::attempt fue exitoso.
            $user = $request->user();

            // 4. Generar un nuevo Token para el usuario
            // Nota: Podrías opcionalmente revocar tokens anteriores aquí si quisieras (ver logout).
            $token = $user->createToken('auth_token')->plainTextToken;

            // 5. Cargar la relación 'role' para obtener el nombre del rol
            $user->load('role');

            // 6. Devolver la respuesta con el token y el rol del usuario
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id'   => $user->role_id,
                    'role_name' => $user->role ? $user->role->name : null, // Incluir el nombre del rol
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
    
    //-----------------------------------------------------------------------------------

    /**
     * Cierra la sesión del usuario (revoca el token actual).
     * * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // El request->user() funciona porque esta ruta está protegida por 'auth:sanctum'
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente. Token revocado.',
        ], 200);
    }
}
