<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\Metodo;
use App\Models\Producto;
use Carbon\Carbon;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function mostrarFormulario(Request $request)
    {
        $carrito = Session::get('carrito', []);
        $metodos = Metodo::all();

        // Si viene desde una cita, crear cita pendiente (peluquería, veterinaria, vacunación)
        if ($request->has('mascota_id') && $request->has('fecha') && $request->has('hora')) {
            $user = Auth::user();
            $cliente = $user?->cliente;

            // Resolver cliente_id: usar cliente del usuario o dueño de la mascota
            $clienteId = $cliente?->id;
            if (!$clienteId && $request->has('mascota_id')) {
                $clienteId = DB::table('mascotas')->where('id', $request->mascota_id)->value('cliente_id');
            }
            if (!$clienteId) {
                return redirect()->route('login')->withErrors('Debes iniciar sesión para agendar y pagar el servicio.');
            }

            // Normalizar hora a HH:MM:SS
            try {
                $horaSql = \Carbon\Carbon::createFromFormat('h:i A', $request->hora)->format('H:i:s');
            } catch (\Exception $e) {
                try {
                    $horaSql = \Carbon\Carbon::createFromFormat('H:i', $request->hora)->format('H:i:s');
                } catch (\Exception $e2) {
                    $horaSql = $request->hora; // fallback
                }
            }

            $tipoServicioId = (int) ($request->tipo_servicio_id ?? 0);

            if ($tipoServicioId === 1 && $request->has('peluquero_id')) {
                // Parsear servicio_detalle "Tipo|Motivo"
                [$tipoServicio, $detalle] = array_pad(explode('|', $request->servicio_detalle, 2), 2, null);

                // Crear cita de peluquería evitando duplicados por (peluquero, fecha, hora)
                $existing = \App\Models\CitaPeluqueria::where('peluquero_id', $request->peluquero_id)
                    ->whereDate('fecha', $request->fecha)
                    ->where('hora', $horaSql)
                    ->first();

                if ($existing && ($existing->cliente_id !== $clienteId || $existing->mascota_id !== (int) $request->mascota_id)) {
                    return redirect()->back()->withErrors('El horario seleccionado ya está reservado.');
                }

                $cita = \App\Models\CitaPeluqueria::firstOrCreate(
                    [
                        'peluquero_id' => $request->peluquero_id,
                        'fecha' => $request->fecha,
                        'hora' => $horaSql,
                    ],
                    [
                        'cliente_id' => $clienteId,
                        'mascota_id' => $request->mascota_id,
                        'servicio_id' => $tipoServicioId,
                        'tipo_servicio' => $tipoServicio,
                        'motivo' => $detalle,
                        'estado' => 'programada',
                        'observaciones' => $request->observaciones ?? '',
                        'nombre_cliente' => $request->nombre_cliente,
                        'correo_cliente' => $request->correo_cliente,
                        'telefono_cliente' => $request->telefono_cliente,
                        'tiene_nudos' => in_array(strtolower($request->tiene_nudos ?? ''), ['si','true','1'], true),
                        'reserva_consumibles' => 10000,
                    ]
                );

                Session::put('cita_peluqueria_id', $cita->id);

                $carrito = [[
                    'tipo' => 'cita_peluqueria',
                    'servicio' => 'Cita de Peluquería',
                    'detalle' => $tipoServicio . ($detalle ? ' - ' . $detalle : ''),
                    'cantidad' => 1,
                    'precio' => 10000,
                ]];
                Session::put('carrito', $carrito);
            } elseif ($tipoServicioId === 2 && $request->has('veterinario_id')) {
                // Crear cita veterinaria evitando duplicados por (veterinario, fecha, hora)
                $existing = \App\Models\Citas::where('veterinario_id', $request->veterinario_id)
                    ->whereDate('fecha', $request->fecha)
                    ->where('hora', $horaSql)
                    ->first();

                if ($existing && ($existing->cliente_id !== $clienteId || $existing->mascota_id !== (int) $request->mascota_id)) {
                    return redirect()->back()->withErrors('El horario seleccionado ya está reservado.');
                }

                $cita = \App\Models\Citas::firstOrCreate(
                    [
                        'veterinario_id' => $request->veterinario_id,
                        'fecha' => $request->fecha,
                        'hora' => $horaSql,
                    ],
                    [
                        'cliente_id' => $clienteId,
                        'mascota_id' => $request->mascota_id,
                        'tipo_servicio_id' => $tipoServicioId,
                        'tipo_servicio' => 'consulta',
                        'estado' => 'programada',
                        'reserva_consumibles' => 10000,
                    ]
                );

                Session::put('cita_veterinaria_id', $cita->id);

                $carrito = [[
                    'tipo' => 'cita_veterinaria',
                    'servicio' => 'Cita Veterinaria',
                    'detalle' => 'Consulta general',
                    'cantidad' => 1,
                    'precio' => 10000,
                ]];
                Session::put('carrito', $carrito);
            } elseif ($tipoServicioId === 3 && $request->has('veterinario_id')) {
                // Crear cita de vacunación evitando duplicados por (veterinario, fecha, hora)
                $existing = \App\Models\Citas::where('veterinario_id', $request->veterinario_id)
                    ->whereDate('fecha', $request->fecha)
                    ->where('hora', $horaSql)
                    ->first();

                if ($existing && ($existing->cliente_id !== $clienteId || $existing->mascota_id !== (int) $request->mascota_id)) {
                    return redirect()->back()->withErrors('El horario seleccionado ya está reservado.');
                }

                $cita = \App\Models\Citas::firstOrCreate(
                    [
                        'veterinario_id' => $request->veterinario_id,
                        'fecha' => $request->fecha,
                        'hora' => $horaSql,
                    ],
                    [
                        'cliente_id' => $clienteId,
                        'mascota_id' => $request->mascota_id,
                        'tipo_servicio_id' => $tipoServicioId,
                        'tipo_servicio' => 'vacunacion',
                        'estado' => 'programada',
                        'motivo' => $request->tipo_vacuna ?? null,
                        'reserva_consumibles' => 10000,
                    ]
                );

                Session::put('cita_vacunacion_id', $cita->id);

                $detalleVac = 'Vacuna' . ($request->tipo_vacuna ? ': ' . $request->tipo_vacuna : '');
                $carrito = [[
                    'tipo' => 'cita_vacunacion',
                    'servicio' => 'Cita de Vacunación',
                    'detalle' => $detalleVac,
                    'cantidad' => 1,
                    'precio' => 10000,
                ]];
                Session::put('carrito', $carrito);
            }
        }

        $prefill = [];
        $user = Auth::user();
        if ($user && $user->cliente) {
            $cliente = $user->cliente;
            $prefill = [
                'nombre' => trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')),
                'direccion' => $cliente->direccion ?? '',
                'telefono' => $cliente->telefono ?? '',
            ];
        } elseif ($user) {
            $prefill = [
                'nombre' => $user->name ?? '',
                'direccion' => '',
                'telefono' => '',
            ];
        }

        // Si es cita de peluquería, usar teléfono del formulario
        if ($request->has('nombre_cliente')) {
            $prefill = [
                'nombre' => $request->nombre_cliente,
                'direccion' => $prefill['direccion'] ?? '',
                'telefono' => $request->telefono_cliente,
            ];
        }

        $departamentos = DB::table('departamentos')->orderBy('nombre')->get();

        return view('pago', compact('carrito', 'metodos', 'prefill', 'departamentos'));
    }

    public function confirmarPago(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'departamento_id' => 'required|exists:departamentos,id',
            'ciudad_id' => 'required|exists:ciudades,id',
            'codigo_postal' => 'required|string',
            'tipo_calle' => 'required|string',
            'direccion' => 'required|string',
            'referencias' => 'nullable|string',
            'telefono' => 'required|string',
            'metodo_id' => 'required|exists:metodo,id',
        ]);

        $carrito = Session::get('carrito', []);
        if (empty($carrito)) {
            return redirect()->back()->withErrors('El carrito está vacío.');
        }

        $user = Auth::user();
        $cliente = $user->cliente;
        if (!$cliente) {
            return redirect()->back()->withErrors('No se encontró cliente asociado al usuario.');
        }

        // Crear la venta
        $venta = new Venta();
        $venta->cliente_id = $cliente->id;
        $venta->fecha = Carbon::now()->toDateString();
        $venta->departamento = DB::table('departamentos')->where('id', $request->departamento_id)->value('nombre');
        $venta->ciudad = DB::table('ciudades')->where('id', $request->ciudad_id)->value('nombre');
        $venta->codigo_postal = $request->codigo_postal;
        $venta->tipo_calle = $request->tipo_calle;
        $venta->direccion = $request->direccion;
        $venta->referencias = $request->referencias;
        $venta->save();

        $totalPago = 0;

        // Guardar productos vendidos (venta_producto)
        foreach ($carrito as $item) {
            $producto = Producto::find($item['producto_id']);
            if (!$producto) {
                return redirect()->back()->withErrors("Producto {$item['nombre']} no encontrado.");
            }

            $cantidad = (int)$item['cantidad'];
            $subTotal = $producto->precio * $cantidad;

            $venta->productos()->attach($producto->id, [
                'cantidad' => $cantidad,
                'sub_total' => $subTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $totalPago += $subTotal;
        }

        // Crear pago
        $pago = new Pago();
        $pago->metodo_id = $request->metodo_id;
        $pago->venta_id = $venta->id;
        $pago->fecha_pago = Carbon::now()->toDateString();
        $pago->monto_pago = $totalPago;
        $pago->referencia_pago = null; // si tienes algo que enviar, úsalo aquí
        $pago->descuento_aplicado = 0; // o null si no hay descuento
        $pago->save();

        // Vaciar carrito y redirigir
        Session::forget('carrito');

        return redirect()->route('pago.exito')->with('success', 'Pago registrado con éxito.');
    }


    public function pagoFallo()
    {
        return redirect('/')->with('error', 'El pago fue rechazado.');
    }

    public function pagoPendiente()
    {
        return redirect('/')->with('warning', 'El pago está pendiente de aprobación.');
    }

    protected MercadoPagoService $mp;

    public function __construct(MercadoPagoService $mp)
    {
        $this->mp = $mp;
    }

    public function iniciarMercadoPago(Request $request)
    {
        // Detectar si es pago de servicio (cita de cualquier tipo)
        $carrito = Session::get('carrito', []);
        $esServicio = !empty($carrito) && isset($carrito[0]['tipo']) && substr($carrito[0]['tipo'], 0, 5) === 'cita_';

        // Validación condicionada
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
        ];
        if (!$esServicio) {
            $rules['departamento_id'] = ['required', 'exists:departamentos,id'];
            $rules['ciudad_id'] = ['required', 'exists:ciudades,id'];
            $rules['codigo_postal'] = ['required', 'string', 'max:20'];
            $rules['tipo_calle'] = ['required', 'string', 'max:50'];
            $rules['direccion'] = ['required', 'string', 'max:255'];
            $rules['referencias'] = ['nullable', 'string', 'max:255'];
        }
        $request->validate($rules);
    
        if (empty($carrito)) {
            return redirect()->back()->withErrors('Tu carrito está vacío. Agrega productos antes de pagar.');
        }
    
        // Validar token de Mercado Pago
        if (!config('services.mercadopago.access_token')) {
            return redirect()->back()->withErrors('Falta configurar MP_ACCESS_TOKEN para iniciar el pago.');
        }
    
        // Validar precios y cantidades de los ítems del carrito
        foreach ($carrito as $ci) {
            $precio = (float)($ci['precio'] ?? 0);
            $cantidad = (int)($ci['cantidad'] ?? 0);
            if ($precio <= 0) {
                return redirect()->back()->withErrors('Hay productos con precio inválido. Verifica tu carrito.');
            }
            if ($cantidad <= 0) {
                return redirect()->back()->withErrors('Hay productos con cantidad inválida. Verifica tu carrito.');
            }
        }
    
        // Determinar método de pago (usar campo metodo_id de la vista)
        $metodoId = $request->metodo_id;
        if (!$metodoId) {
            $metodoId = Metodo::where('descripcion', 'Mercado Pago')->value('id');
            if (!$metodoId) {
                $metodo = Metodo::firstOrCreate(['descripcion' => 'Mercado Pago']);
                $metodoId = $metodo->id;
            }
        }
        Session::put('metodo_pago_seleccionado', $metodoId);

        // Preparar items para MP
        $items = [];
        foreach ($carrito as $item) {
            if (isset($item['tipo']) && substr($item['tipo'], 0, 5) === 'cita_') {
                $title = ($item['servicio'] ?? 'Servicio') . (isset($item['detalle']) ? (' - ' . $item['detalle']) : '');
            } else {
                $title = $item['nombre'] ?? 'Producto';
            }
            $items[] = [
                'title' => $title,
                'quantity' => (int) ($item['cantidad'] ?? 1),
                'unit_price' => (float) ($item['precio'] ?? 0),
                'currency_id' => 'COP',
            ];
        }

        $user = Auth::user();
        $backUrls = [
            'success' => route('pago.exito'),
            'failure' => route('pago.fallo'),
            'pending' => route('pago.pendiente'),
        ];
        $notificationUrl = url('/pago/notificacion');
        $externalReference = ($esServicio ? 'SERV-' : 'CART-') . ($user?->id ?? 'guest') . '-' . now()->format('YmdHis');

        try {
            $pref = $this->mp->createPreference(
                $items,
                $backUrls,
                $user ? ['email' => $user->email] : null,
                $externalReference,
                $notificationUrl
            );
        } catch (\Throwable $e) {
            \Log::error('Error creando preferencia MP', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors('No se pudo iniciar el pago en Mercado Pago. Verifica token y precios de productos.');
        }

        $initPoint = $pref['init_point'] ?? $pref['sandbox_init_point'] ?? null;
        if (!$initPoint) {
            return redirect()->back()->withErrors('No se obtuvo URL de pago de Mercado Pago.');
        }

        return \Illuminate\Support\Facades\Redirect::away($initPoint);
    }

    public function notificacion(Request $request)
    {
        Log::info('Webhook Mercado Pago', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);
        return response('OK');
    }

    public function pagoExito(Request $request)
    {
            // Actualizar estado de cita si existe en sesión
            $citaIdP = Session::get('cita_peluqueria_id');
            $citaIdV = Session::get('cita_veterinaria_id');
            $citaIdVa = Session::get('cita_vacunacion_id');
            if ($citaIdP) {
                $cita = \App\Models\CitaPeluqueria::find($citaIdP);
                if ($cita) {
                    $cita->estado = 'confirmada';
                    $cita->save();
                }
            }
            if ($citaIdV) {
                $cita = \App\Models\Citas::find($citaIdV);
                if ($cita) {
                    $cita->estado = 'confirmada';
                    $cita->save();
                }
            }
            if ($citaIdVa) {
                $cita = \App\Models\Citas::find($citaIdVa);
                if ($cita) {
                    $cita->estado = 'confirmada';
                    $cita->save();
                }
            }

            // Mantener lógica existente de venta/pago si aplica
            $carrito = Session::get('carrito', []);
        if (empty($carrito)) {
            return redirect()->back()->withErrors('El carrito está vacío.');
        }

        $user = Auth::user();
        $cliente = $user->cliente;
        if (!$cliente) {
            return redirect()->back()->withErrors('No se encontró cliente asociado al usuario.');
        }

        // Crear la venta
        $venta = new Venta();
        $venta->cliente_id = $cliente->id;
        $venta->fecha = Carbon::now()->toDateString();
        $venta->departamento = DB::table('departamentos')->where('id', $request->departamento_id)->value('nombre');
        $venta->ciudad = DB::table('ciudades')->where('id', $request->ciudad_id)->value('nombre');
        $venta->codigo_postal = $request->codigo_postal;
        $venta->tipo_calle = $request->tipo_calle;
        $venta->direccion = $request->direccion;
        $venta->referencias = $request->referencias;
        $venta->save();

        $totalPago = 0;
        foreach ($carrito as $item) {
            $producto = Producto::find($item['producto_id']);
            if ($producto) {
                $cantidad = (int) $item['cantidad'];
                $subTotal = ($producto->precio ?? ($item['precio'] ?? 0)) * $cantidad;
                $venta->productos()->attach($producto->id, [
                    'cantidad' => $cantidad,
                    'sub_total' => $subTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $totalPago += $subTotal;
            }
        }

        $pago = new Pago();
        $pago->metodo_id = Session::get('metodo_pago_seleccionado');
        $pago->venta_id = $venta->id;
        $pago->fecha_pago = Carbon::now()->toDateString();
        $pago->monto_pago = $totalPago;
        $pago->referencia_pago = $request->get('payment_id') ?: $request->get('collection_id');
        $pago->descuento_aplicado = 0;
        $pago->save();

        Session::forget('carrito');
        Session::forget('metodo_pago_seleccionado');
        Session::forget('cita_peluqueria_id');
        Session::forget('cita_veterinaria_id');
        Session::forget('cita_vacunacion_id');

        return redirect('/')->with('success', '¡Pago realizado con éxito!');
    }

    public function getCiudadesPorDepartamento($departamentoId)
    {
        $ciudades = DB::table('ciudades')
            ->where('departamento_id', $departamentoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    
        return response()->json($ciudades);
    }
}
