@extends('layouts.app')

@section('title', 'Registro de prestadores turísticos')
@section('description', 'Formulario departamental para prestadores de servicios turísticos de Tarija.')

@section('content')
<section class="bg-pattern pb-16 pt-32 text-white">
    <div class="container-custom">
        <p class="text-sm font-black uppercase tracking-[.2em] text-[#eadfd2]">Directorio departamental</p>
        <h1 class="mt-3 max-w-4xl font-display text-4xl font-black md:text-5xl">Registro de prestadores de servicios turísticos</h1>
        <p class="mt-4 max-w-3xl text-white/80">Complete la información de su actividad. Los campos marcados con * son obligatorios.</p>
    </div>
</section>

<section class="bg-[#f8f3ec] py-14" x-data="{ type: @js(old('provider_type', '')), step: 1, completed: 1 }" @provider-step.window="step = $event.detail; completed = Math.max(completed, step)">
    <div class="container-custom max-w-5xl">
        @if(session('success'))
            <div class="mb-8 rounded-2xl border border-green-200 bg-green-50 p-5 font-semibold text-green-800"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800">
                <p class="font-bold">Revise los campos indicados antes de enviar.</p>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="provider-registration-form" class="space-y-8" method="POST" action="{{ route('prestadores.store') }}" enctype="multipart/form-data" autocomplete="off" novalidate>
            @csrf

            <nav class="provider-wizard" aria-label="Progreso del registro">
                @foreach([
                    1 => ['Datos básicos', 'Identidad y contacto', 'fa-user'],
                    2 => ['Actividad', 'Ubicación y servicios', 'fa-briefcase'],
                    3 => ['Documentos', 'Archivos de respaldo', 'fa-folder-open'],
                    4 => ['Revisión', 'Confirmar y enviar', 'fa-circle-check'],
                ] as $wizardStep => [$wizardTitle, $wizardSubtitle, $wizardIcon])
                    <button
                        type="button"
                        class="provider-wizard-step"
                        :class="{ 'is-active': step === {{ $wizardStep }}, 'is-complete': completed > {{ $wizardStep }} }"
                        @click="if ({{ $wizardStep }} < step || {{ $wizardStep }} <= completed) { step = {{ $wizardStep }}; scrollProviderWizard() }"
                    >
                        <span><i class="fa-solid {{ $wizardIcon }}"></i><b>{{ $wizardStep }}</b></span>
                        <small>{{ $wizardSubtitle }}</small>
                        <strong>{{ $wizardTitle }}</strong>
                    </button>
                @endforeach
            </nav>
            <div id="wizard-validation-error" class="wizard-validation-error hidden" role="alert" aria-live="assertive">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><strong>Faltan datos para continuar</strong><p data-wizard-error-message></p></div>
            </div>

            <div class="provider-step-panel" data-provider-step="1" x-show="step === 1" x-transition.opacity>
                <div class="provider-step-intro"><span>1</span><div><small>Primer paso</small><h2>Cuéntanos quién eres</h2><p>Completa los datos principales y la forma en que podemos contactarte.</p></div></div>
            <x-provider-section title="Datos generales" icon="fa-building">
                <div class="md:col-span-2">
                    <label class="provider-label" for="provider_type">Tipo de prestador turístico *</label>
                    <select class="provider-input" id="provider_type" name="provider_type" x-model="type" required>
                        <option value="">Seleccione una opción</option>
                        @foreach(['hospedaje' => 'Hospedaje', 'gastronomia' => 'Gastronomía', 'agencia_viajes' => 'Agencia de viajes', 'operadora_turismo' => 'Operadora de turismo', 'guia_departamental' => 'Guía departamental', 'transporte' => 'Transporte turístico', 'artesania_comercio' => 'Artesanía o comercio turístico', 'actividad_turistica' => 'Actividad recreativa o turística', 'otro' => 'Otro'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('provider_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('provider_type') <p class="provider-error">{{ $message }}</p> @enderror
                </div>
                <div x-show="type === 'otro'" x-cloak><x-provider-input name="provider_type_other" label="Otro tipo de prestador *" x-bind:required="type === 'otro'" /></div>
                <x-provider-input name="commercial_name" label="Nombre comercial *" required />
                <x-provider-input name="business_name" label="Razón social (si corresponde)" />
                <x-provider-input name="nit" label="NIT" />
                <div class="md:col-span-2 rounded-xl bg-gray-50 p-4">
                    <label class="flex items-center gap-3 font-semibold"><input class="rounded border-gray-300 text-red-800" type="checkbox" name="has_tourism_license" value="1" @checked(old('has_tourism_license'))> Cuenta con licencia turística departamental</label>
                    <div class="mt-4 grid gap-4 md:grid-cols-2"><x-provider-input type="date" name="tourism_license_issued_at" label="Fecha de emisión" /><x-provider-input type="date" name="tourism_license_renewed_at" label="Fecha de renovación" /></div>
                </div>
                <x-provider-input name="legal_representative" label="Representante legal o propietario *" required />
                <x-provider-input name="identity_document" label="Documento de identidad *" required />
            </x-provider-section>

            <x-provider-section title="Datos de contacto" icon="fa-address-book">
                <x-provider-input name="landline" label="Teléfono fijo" />
                <x-provider-input name="whatsapp" label="Celular / WhatsApp *" required />
                <x-provider-input type="email" name="email" label="Correo electrónico *" required />
                <x-provider-input type="url" name="website" label="Página web" placeholder="https://" />
                <x-provider-input name="facebook" label="Facebook" />
                <x-provider-input name="instagram" label="Instagram" />
                <x-provider-input name="tiktok" label="TikTok" />
                <x-provider-input name="x_url" label="X (antes Twitter)" type="url" placeholder="https://x.com/tu-cuenta" />
                <x-provider-input name="youtube_url" label="YouTube" type="url" placeholder="https://www.youtube.com/@tu-canal" />
                <x-provider-input name="other_social_network" label="Otra red social" />
            </x-provider-section>
                <div class="provider-step-actions is-end">
                    <button class="provider-next" type="button" @click="if (validateProviderStep(1)) { step = 2; completed = Math.max(completed, 2); scrollProviderWizard() }">Continuar a actividad <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="provider-step-panel" data-provider-step="2" x-cloak x-show="step === 2" x-transition.opacity>
                <div class="provider-step-intro"><span>2</span><div><small>Segundo paso</small><h2>Describe tu actividad</h2><p>Indica dónde trabajas y selecciona los servicios que ofreces.</p></div></div>
            <x-provider-section title="Ubicación" icon="fa-location-dot">
                <x-provider-input name="department" label="Departamento *" value="{{ old('department', 'Tarija') }}" required />
                <x-provider-input name="municipality" label="Municipio *" required />
                <div class="md:col-span-2"><x-provider-input name="address" label="Dirección *" required /></div>
                <div class="md:col-span-2"><x-provider-input name="maps_location" label="Coordenadas o enlace de Google Maps" /></div>
            </x-provider-section>

            <div x-show="type === 'hospedaje'" x-cloak>
                <x-provider-section title="Información para hospedajes" icon="fa-hotel">
                    <x-provider-select name="lodging_type" label="Tipo de establecimiento *" :options="['hotel'=>'Hotel','hostal'=>'Hostal','residencial'=>'Residencial','apart_hotel'=>'Apart Hotel','alojamiento'=>'Alojamiento','casa_huespedes'=>'Casa de huéspedes','otro'=>'Otro']" x-bind:required="type === 'hospedaje'" />
                    <x-provider-input name="lodging_type_other" label="Otro tipo" />
                    <x-provider-input type="number" min="0" name="room_count" label="Número de habitaciones" />
                    <x-provider-input type="number" min="0" name="guest_capacity" label="Capacidad máxima de huéspedes" />
                    <div class="md:col-span-2"><x-provider-checks name="lodging_services" label="Servicios disponibles" :options="['wifi'=>'Wi-Fi','restaurante'=>'Restaurante','piscina'=>'Piscina','estacionamiento'=>'Estacionamiento','aire_acondicionado'=>'Aire acondicionado','desayuno'=>'Desayuno','accesibilidad'=>'Accesibilidad']" /><div class="mt-4"><x-provider-input name="lodging_services_other" label="Otros servicios" /></div></div>
                </x-provider-section>
            </div>

            <div x-show="type === 'agencia_viajes'" x-cloak>
                <x-provider-section title="Información para agencias de viajes" icon="fa-plane-departure">
                    <x-provider-select name="agency_type" label="Tipo de agencia *" :options="['emisiva'=>'Emisiva','receptiva'=>'Receptiva','mayorista'=>'Mayorista','minorista'=>'Minorista']" x-bind:required="type === 'agencia_viajes'" />
                    <div class="md:col-span-2"><x-provider-checks name="agency_services" label="Servicios que ofrece" :options="['boletos'=>'Venta de boletos','hoteles'=>'Reservas de hoteles','transporte'=>'Transporte turístico','seguros'=>'Seguros de viaje']" /><div class="mt-4"><x-provider-input name="agency_services_other" label="Otro servicio" /></div></div>
                </x-provider-section>
            </div>

            <div x-show="type === 'operadora_turismo'" x-cloak>
                <x-provider-section title="Información para operadoras de turismo" icon="fa-route">
                    <div class="md:col-span-2"><x-provider-checks name="tourism_modalities" label="Modalidades de turismo" :options="['cultural'=>'Cultural','naturaleza'=>'Naturaleza','aventura'=>'Aventura','comunitario'=>'Comunitario','rural'=>'Rural','gastronomico'=>'Gastronómico','religioso'=>'Religioso']" /></div>
                    <x-provider-input name="package_types" label="Tipos de paquetes" />
                    <div class="md:col-span-2"><label class="provider-label" for="main_destinations">Principales destinos que opera</label><textarea class="provider-input min-h-28" id="main_destinations" name="main_destinations">{{ old('main_destinations') }}</textarea></div>
                </x-provider-section>
            </div>

            <div x-show="type === 'guia_departamental'" x-cloak>
                <x-provider-section title="Información para guías departamentales" icon="fa-person-hiking">
                    <div class="md:col-span-2 rounded-xl bg-gray-50 p-4"><label class="flex items-center gap-3 font-semibold"><input class="rounded border-gray-300 text-red-800" type="checkbox" name="has_guide_credential" value="1" @checked(old('has_guide_credential'))> Cuenta con credencial de guía turístico</label><div class="mt-4 grid gap-4 md:grid-cols-2"><x-provider-input type="date" name="guide_credential_issued_at" label="Fecha de emisión" /><x-provider-input type="date" name="guide_credential_renewed_at" label="Fecha de renovación" /></div></div>
                    <div class="md:col-span-2"><x-provider-checks name="languages" label="Idiomas que domina" :options="['espanol'=>'Español','ingles'=>'Inglés','frances'=>'Francés','portugues'=>'Portugués','aleman'=>'Alemán']" /><div class="mt-4"><x-provider-input name="language_other" label="Otro idioma" /></div></div>
                    <div class="md:col-span-2"><x-provider-checks name="specialties" label="Especialidad" :options="['cultural'=>'Turismo cultural','naturaleza'=>'Naturaleza','aventura'=>'Aventura','arqueologia'=>'Arqueología','gastronomia'=>'Gastronomía']" /><div class="mt-4"><x-provider-input name="specialty_other" label="Otra especialidad" /></div></div>
                    <x-provider-input type="number" min="0" max="100" name="experience_years" label="Años de experiencia" />
                </x-provider-section>
            </div>
                <div class="provider-step-actions">
                    <button class="provider-back" type="button" @click="step = 1; scrollProviderWizard()"><i class="fa-solid fa-arrow-left"></i> Volver</button>
                    <button class="provider-next" type="button" @click="if (validateProviderStep(2)) { step = 3; completed = Math.max(completed, 3); scrollProviderWizard() }">Continuar a documentos <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="provider-step-panel" data-provider-step="3" x-cloak x-show="step === 3" x-transition.opacity>
                <div class="provider-step-intro"><span>3</span><div><small>Tercer paso</small><h2>Adjunta tus respaldos</h2><p>Puedes revisar, cambiar o quitar cada documento antes de continuar.</p></div></div>
            <x-provider-section title="Documentación" icon="fa-folder-open">
                <div class="document-guide md:col-span-2">
                    <span class="document-guide-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                    <div>
                        <h3>Sube tus documentos</h3>
                        <p>Selecciona cada archivo desde tu celular o computadora. Puedes usar PDF, JPG, PNG o WEBP.</p>
                    </div>
                    <span class="document-limit"><i class="fa-solid fa-weight-hanging"></i> 10 MB por archivo</span>
                </div>
                @php
                    $documentFields = [
                        'nit' => ['Copia del NIT', 'Documento emitido por Impuestos Nacionales.', 'fa-file-invoice'],
                        'licencia' => ['Licencia turística departamental', 'Licencia vigente, si cuentas con ella.', 'fa-certificate'],
                        'identidad' => ['Documento de identidad', 'Anverso y reverso en un solo PDF o imagen clara.', 'fa-id-card'],
                        'fotografia' => ['Fotografía del establecimiento o personal', 'Una imagen nítida que permita identificar el servicio.', 'fa-camera'],
                        'logo' => ['Logo de la empresa', 'Opcional · Preferentemente PNG o WEBP.', 'fa-image'],
                        'seprec' => ['SEPREC', 'Matrícula o certificado del registro de comercio.', 'fa-building-circle-check'],
                        'comprobante' => ['Comprobante de pago', 'Comprobante de la tasa administrativa.', 'fa-receipt'],
                    ];
                @endphp
                @foreach($documentFields as $key => [$label, $help, $icon])
                    <div class="document-upload" data-document-upload>
                        <div class="document-upload-heading">
                            <span class="document-upload-icon"><i class="fa-solid {{ $icon }}"></i></span>
                            <span>
                                <strong>{{ $label }}</strong>
                                <small>{{ $help }}</small>
                            </span>
                        </div>
                        <label class="document-upload-action" for="document_{{ $key }}">
                            <input class="provider-document sr-only" id="document_{{ $key }}" type="file" name="documents[{{ $key }}]" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <span class="document-upload-button"><i class="fa-solid fa-plus"></i><span>Seleccionar archivo</span></span>
                            <span class="document-upload-status" aria-live="polite">
                                <i class="fa-regular fa-file"></i>
                                <span data-file-name>Ningún archivo seleccionado</span>
                                <small data-file-size>PDF o imagen · máximo 10 MB</small>
                            </span>
                        </label>
                        <button class="document-remove hidden" type="button" data-remove-file aria-label="Quitar archivo"><i class="fa-solid fa-trash-can"></i> Quitar</button>
                    </div>
                @endforeach
                <div class="document-total md:col-span-2">
                    <span><i class="fa-solid fa-shield-halved"></i> Tus archivos se almacenan de forma privada y solo el personal autorizado puede revisarlos.</span>
                    <strong id="document-total-size">0 MB de 70 MB</strong>
                </div>
                <div id="document-size-error" class="hidden md:col-span-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800" role="alert"></div>
            </x-provider-section>
                <div class="provider-step-actions">
                    <button class="provider-back" type="button" @click="step = 2; scrollProviderWizard()"><i class="fa-solid fa-arrow-left"></i> Volver</button>
                    <button class="provider-next" type="button" @click="if (validateProviderStep(3)) { refreshProviderReview(); step = 4; completed = 4; scrollProviderWizard() }">Revisar información <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="provider-step-panel" data-provider-step="4" x-cloak x-show="step === 4" x-transition.opacity>
                <div class="provider-step-intro"><span>4</span><div><small>Último paso</small><h2>Revisa antes de enviar</h2><p>Confirma que los datos principales sean correctos. Puedes volver a cualquier paso para modificarlos.</p></div></div>
                <div class="provider-review">
                    <div><i class="fa-solid fa-building"></i><span><small>Nombre comercial</small><strong data-review="commercial_name">Sin completar</strong></span></div>
                    <div><i class="fa-solid fa-briefcase"></i><span><small>Tipo de prestador</small><strong data-review="provider_type">Sin completar</strong></span></div>
                    <div><i class="fa-solid fa-location-dot"></i><span><small>Municipio</small><strong data-review="municipality">Sin completar</strong></span></div>
                    <div><i class="fa-solid fa-envelope"></i><span><small>Correo</small><strong data-review="email">Sin completar</strong></span></div>
                    <div><i class="fa-solid fa-phone"></i><span><small>WhatsApp</small><strong data-review="whatsapp">Sin completar</strong></span></div>
                    <div><i class="fa-solid fa-paperclip"></i><span><small>Documentos</small><strong data-review="documents">0 seleccionados</strong></span></div>
                </div>
            <x-provider-section title="Declaración y firma" icon="fa-file-signature">
                <div class="md:col-span-2 rounded-xl border border-red-100 bg-red-50 p-5 text-sm leading-6 text-gray-700">Declaro que la información proporcionada es verdadera y autorizo su uso para fines de registro, promoción y actualización del directorio de prestadores de servicios turísticos.</div>
                <label class="md:col-span-2 flex items-start gap-3 font-semibold"><input class="mt-1 rounded border-gray-300 text-red-800" type="checkbox" name="declaration_accepted" value="1" required @checked(old('declaration_accepted'))><span>Acepto la declaración *</span></label>
                <x-provider-input name="applicant_name" label="Nombre del solicitante *" required />
                <x-provider-input name="application_place" label="Lugar *" required />
                <x-provider-input type="date" name="application_date" label="Fecha *" value="{{ old('application_date', now()->toDateString()) }}" required />
            </x-provider-section>

                <div class="provider-step-actions">
                    <button class="provider-back" type="button" @click="step = 3; scrollProviderWizard()"><i class="fa-solid fa-arrow-left"></i> Volver a documentos</button>
                    <button class="provider-submit" type="submit"><i class="fa-solid fa-paper-plane"></i> Enviar registro</button>
                </div>
            </div>
        </form>
    </div>
</section>

@if(session('success'))
    <div class="fixed inset-0 z-[100] grid place-items-center bg-gray-950/65 p-4 backdrop-blur-sm" x-data="{ open: true }" x-show="open" x-transition.opacity role="dialog" aria-modal="true" aria-labelledby="success-modal-title">
        <div class="w-full max-w-md rounded-3xl bg-white p-8 text-center shadow-2xl" @click.outside="open = false">
            <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-green-100 text-4xl text-green-700"><i class="fa-solid fa-check"></i></div>
            <h2 id="success-modal-title" class="mt-6 text-3xl font-black text-gray-950">¡Registro exitoso!</h2>
            <p class="mt-3 leading-7 text-gray-600">La información del prestador fue guardada correctamente y será revisada por el equipo de turismo.</p>
            <button class="btn-primary mt-7 w-full justify-center" type="button" @click="open = false">Aceptar</button>
        </div>
    </div>
@endif

@if($errors->has('commercial_name') && collect($errors->get('commercial_name'))->contains(fn ($message) => str_contains($message, 'Ya existe')))
    <div class="fixed inset-0 z-[100] grid place-items-center bg-gray-950/65 p-4 backdrop-blur-sm" x-data="{ open: true }" x-show="open" x-transition.opacity role="alertdialog" aria-modal="true" aria-labelledby="duplicate-modal-title">
        <div class="w-full max-w-md rounded-3xl bg-white p-8 text-center shadow-2xl" @click.outside="open = false">
            <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-amber-100 text-4xl text-amber-700"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h2 id="duplicate-modal-title" class="mt-6 text-2xl font-black text-gray-950">Nombre ya registrado</h2>
            <p class="mt-3 leading-7 text-gray-600">Ya existe un prestador con el nombre comercial <strong>{{ old('commercial_name') }}</strong>. Verifique el nombre o comuníquese con el equipo de turismo si necesita actualizar ese registro.</p>
            <button class="mt-7 w-full rounded-xl bg-amber-600 px-6 py-3 font-bold text-white transition hover:bg-amber-700" type="button" @click="open = false">Revisar nombre</button>
        </div>
    </div>
@endif

<style>
    .provider-card { border-radius: 1.5rem; border: 1px solid #eee2da; background: white; padding: clamp(1.25rem, 4vw, 2rem); box-shadow: 0 18px 45px rgba(45, 11, 18, .07); }
    .provider-label { display: block; margin-bottom: .45rem; font-size: .875rem; font-weight: 700; color: #374151; }
    .provider-input { display: block; width: 100%; border-radius: .75rem; border-color: #d1d5db; background: #fff; }
    #provider-registration-form .provider-input,
    #provider-registration-form .provider-input option,
    #provider-registration-form input::placeholder,
    #provider-registration-form textarea::placeholder {
        text-transform: uppercase;
    }
    #provider-registration-form .provider-label,
    #provider-registration-form fieldset label {
        text-transform: uppercase;
    }
    .provider-input:focus { border-color: #7e3444; --tw-ring-color: rgba(126, 52, 68, .2); }
    .provider-error { margin-top: .35rem; font-size: .8rem; color: #b91c1c; }
    .wizard-validation-error { display:flex; align-items:flex-start; gap:.8rem; border:1px solid #f2b8b5; border-radius:1rem; color:#991b1b; background:#fff1f1; padding:1rem; box-shadow:0 8px 22px rgba(153,27,27,.08); }
    .wizard-validation-error.hidden { display:none; }
    .wizard-validation-error > i { margin-top:.15rem; }
    .wizard-validation-error strong { display:block; font-size:.85rem; }
    .wizard-validation-error p { margin:.2rem 0 0; font-size:.75rem; line-height:1.45; }
    .provider-input[aria-invalid="true"] { border-color:#dc2626; background:#fff7f7; box-shadow:0 0 0 3px rgba(220,38,38,.1); }
    .provider-wizard { position:sticky; top:5.5rem; z-index:20; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.5rem; padding:.65rem; border:1px solid #eadfd9; border-radius:1.25rem; background:rgba(255,255,255,.94); box-shadow:0 14px 35px rgba(69,10,10,.09); backdrop-filter:blur(12px); }
    .provider-wizard-step { position:relative; display:grid; min-width:0; grid-template-columns:auto minmax(0,1fr); column-gap:.65rem; align-items:center; border:0; border-radius:1rem; background:transparent; padding:.75rem; text-align:left; cursor:pointer; transition:.25s ease; }
    .provider-wizard-step > span { display:grid; width:2.6rem; height:2.6rem; grid-row:span 2; place-items:center; border-radius:.8rem; color:#8b6f74; background:#f5eeee; transition:.25s; }
    .provider-wizard-step > span b { display:none; }
    .provider-wizard-step small { overflow:hidden; color:#9a8d89; font-size:.62rem; text-overflow:ellipsis; white-space:nowrap; }
    .provider-wizard-step strong { overflow:hidden; color:#514749; font-size:.78rem; text-overflow:ellipsis; white-space:nowrap; }
    .provider-wizard-step.is-active { background:#fff1f2; box-shadow:inset 0 0 0 1px #e9c9ce; }
    .provider-wizard-step.is-active > span { color:#fff; background:#762033; box-shadow:0 7px 15px rgba(118,32,51,.22); }
    .provider-wizard-step.is-active strong { color:#681526; }
    .provider-wizard-step.is-complete > span { color:#fff; background:#287a4a; }
    .provider-wizard-step.is-complete > span i { display:none; }
    .provider-wizard-step.is-complete > span b { display:block; }
    .provider-step-panel { display:grid; gap:2rem; }
    .provider-step-intro { display:flex; align-items:center; gap:1rem; padding:1rem 1.2rem; border-radius:1.2rem; color:#fff; background:linear-gradient(135deg,#571624,#762033); box-shadow:0 12px 30px rgba(87,22,36,.15); }
    .provider-step-intro > span { display:grid; width:3rem; height:3rem; flex:0 0 auto; place-items:center; border-radius:1rem; background:rgba(255,255,255,.14); font-size:1.1rem; font-weight:900; }
    .provider-step-intro small { color:#e9cbd1; font-size:.62rem; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
    .provider-step-intro h2 { margin:.1rem 0 0; font-size:1.2rem; font-weight:900; }
    .provider-step-intro p { margin:.2rem 0 0; color:rgba(255,255,255,.76); font-size:.78rem; }
    .provider-step-actions { display:flex; justify-content:space-between; gap:1rem; padding-top:.5rem; }
    .provider-step-actions.is-end { justify-content:flex-end; }
    .provider-back,.provider-next,.provider-submit { display:inline-flex; min-height:3rem; align-items:center; justify-content:center; gap:.65rem; border-radius:.9rem; padding:.8rem 1.35rem; font-size:.82rem; font-weight:900; cursor:pointer; transition:.2s; }
    .provider-back { border:1px solid #d9cfcb; color:#665b58; background:#fff; }
    .provider-back:hover { border-color:#9f5966; color:#762033; background:#fff8f7; }
    .provider-next,.provider-submit { border:0; color:#fff; background:#762033; box-shadow:0 8px 18px rgba(118,32,51,.2); }
    .provider-next:hover,.provider-submit:hover { background:#971d32; transform:translateY(-1px); }
    .provider-review { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.75rem; }
    .provider-review > div { display:flex; min-width:0; align-items:center; gap:.8rem; border:1px solid #eadfd9; border-radius:1rem; background:#fff; padding:1rem; }
    .provider-review > div > i { display:grid; width:2.5rem; height:2.5rem; flex:0 0 auto; place-items:center; border-radius:.8rem; color:#762033; background:#f8ecee; }
    .provider-review span { min-width:0; }
    .provider-review small { display:block; color:#8b817c; font-size:.65rem; }
    .provider-review strong { display:block; overflow:hidden; margin-top:.15rem; color:#342b2d; font-size:.82rem; text-overflow:ellipsis; white-space:nowrap; }
    .document-guide { display: flex; align-items: center; gap: 1rem; border-radius: 1.25rem; border: 1px solid #ead8d4; background: linear-gradient(135deg, #fff8f5, #f7efea); padding: 1.15rem; }
    .document-guide-icon { display: grid; width: 3.25rem; height: 3.25rem; flex: 0 0 auto; place-items: center; border-radius: 1rem; background: #7f1d2d; color: white; font-size: 1.25rem; box-shadow: 0 10px 22px rgba(111, 29, 44, .2); }
    .document-guide h3 { font-size: 1rem; font-weight: 900; color: #3f0710; }
    .document-guide p { margin-top: .2rem; font-size: .82rem; line-height: 1.4; color: #6b7280; }
    .document-limit { margin-left: auto; flex: 0 0 auto; border-radius: 999px; background: white; padding: .55rem .8rem; font-size: .72rem; font-weight: 800; color: #7f1d2d; box-shadow: 0 5px 15px rgba(69, 10, 10, .08); }
    .document-upload { position: relative; display: flex; min-width: 0; flex-direction: column; gap: .9rem; border-radius: 1.25rem; border: 2px dashed #e4d4cf; background: #fffdfc; padding: 1rem; transition: .25s ease; }
    .document-upload:hover, .document-upload:focus-within { border-color: #b76a77; background: #fff8f6; box-shadow: 0 12px 28px rgba(69, 10, 10, .08); transform: translateY(-2px); }
    .document-upload.has-file { border-style: solid; border-color: #86b59a; background: #f4fbf6; }
    .document-upload.has-error { border-style: solid; border-color: #dc2626; background: #fff5f5; }
    .document-upload-heading { display: flex; min-width: 0; align-items: flex-start; gap: .75rem; }
    .document-upload-icon { display: grid; width: 2.5rem; height: 2.5rem; flex: 0 0 auto; place-items: center; border-radius: .8rem; background: #f9ecea; color: #7f1d2d; }
    .document-upload-heading strong { display: block; color: #291b1e; font-size: .9rem; }
    .document-upload-heading small { display: block; margin-top: .2rem; color: #6b7280; font-size: .72rem; line-height: 1.35; }
    .document-upload-action { display: grid; cursor: pointer; gap: .7rem; }
    .document-upload-button { display: flex; min-height: 2.8rem; align-items: center; justify-content: center; gap: .55rem; border-radius: .8rem; background: #781b2c; padding: .7rem 1rem; color: white; font-size: .82rem; font-weight: 900; box-shadow: 0 8px 16px rgba(111, 29, 44, .18); transition: .2s ease; }
    .document-upload-action:hover .document-upload-button { background: #991b1b; transform: translateY(-1px); }
    .document-upload-status { display: grid; min-width: 0; grid-template-columns: auto minmax(0, 1fr); column-gap: .55rem; align-items: center; border-radius: .75rem; background: #f6f3f2; padding: .65rem .75rem; color: #6b7280; }
    .document-upload-status > i { grid-row: span 2; }
    .document-upload-status span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .75rem; font-weight: 700; }
    .document-upload-status small { font-size: .66rem; }
    .document-upload.has-file .document-upload-status { background: #e9f7ed; color: #166534; }
    .document-upload.has-file .document-upload-button { background: #166534; }
    .document-remove { align-self: flex-end; color: #b91c1c; font-size: .72rem; font-weight: 800; }
    .document-total { display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-radius: 1rem; background: #f5f1ee; padding: .9rem 1rem; color: #6b7280; font-size: .75rem; }
    .document-total strong { flex: 0 0 auto; color: #4a0711; }
    @media (max-width: 639px) {
        .provider-wizard { top:5rem; display:flex; overflow-x:auto; scroll-snap-type:x mandatory; }
        .provider-wizard-step { min-width:11rem; scroll-snap-align:start; }
        .provider-step-intro p { display:none; }
        .provider-step-actions { align-items:stretch; flex-direction:column-reverse; }
        .provider-step-actions button { width:100%; }
        .provider-review { grid-template-columns:1fr; }
        .document-guide { align-items: flex-start; flex-wrap: wrap; }
        .document-limit { margin-left: 4.25rem; margin-top: -.5rem; }
        .document-total { align-items: flex-start; flex-direction: column; }
    }
</style>
<script>
    window.scrollProviderWizard = function () {
        requestAnimationFrame(function () {
            document.querySelector('.provider-wizard')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    };

    window.validateProviderStep = function (step) {
        const panel = document.querySelector('[data-provider-step="' + step + '"]');
        if (!panel) return true;
        const errorBox = document.getElementById('wizard-validation-error');
        const errorMessage = errorBox?.querySelector('[data-wizard-error-message]');

        const fields = Array.from(panel.querySelectorAll('input, select, textarea'))
            .filter(function (field) { return !field.disabled && field.type !== 'hidden' && field.offsetParent !== null; });

        fields.forEach(function (field) { field.removeAttribute('aria-invalid'); });
        const invalidFields = fields.filter(function (field) { return !field.checkValidity(); });
        if (!invalidFields.length) {
            errorBox?.classList.add('hidden');
            return true;
        }

        invalidFields.forEach(function (field) { field.setAttribute('aria-invalid', 'true'); });
        const labels = invalidFields.map(function (field) {
            const directLabel = panel.querySelector('label[for="' + field.id + '"] .provider-label, label[for="' + field.id + '"]');
            const wrappingLabel = field.closest('label');
            return (directLabel?.textContent || wrappingLabel?.textContent || field.name)
                .replace('*', '')
                .replace(/\s+/g, ' ')
                .trim();
        }).filter(Boolean);

        if (errorMessage) {
            errorMessage.textContent = 'Completa o corrige: ' + labels.slice(0, 5).join(', ') + (labels.length > 5 ? ' y ' + (labels.length - 5) + ' campo(s) más.' : '.');
        }
        errorBox?.classList.remove('hidden');

        const invalid = invalidFields[0];
        errorBox?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () { invalid.focus({ preventScroll: true }); }, 350);
        return false;
    };

    window.refreshProviderReview = function () {
        const form = document.getElementById('provider-registration-form');
        if (!form) return;
        const value = function (name) { return form.elements[name]?.value?.trim() || 'Sin completar'; };
        const typeSelect = form.elements.provider_type;
        const type = typeSelect?.selectedOptions?.[0]?.text || 'Sin completar';
        const files = Array.from(form.querySelectorAll('.provider-document')).filter(function (input) { return input.files?.length; }).length;
        const values = {
            commercial_name: value('commercial_name'),
            provider_type: type,
            municipality: value('municipality'),
            email: value('email'),
            whatsapp: value('whatsapp'),
            documents: files + (files === 1 ? ' seleccionado' : ' seleccionados'),
        };
        Object.entries(values).forEach(function ([key, text]) {
            const target = document.querySelector('[data-review="' + key + '"]');
            if (target) target.textContent = text;
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('provider-registration-form');
        const inputs = Array.from(document.querySelectorAll('.provider-document'));
        const errorBox = document.getElementById('document-size-error');
        const totalSizeLabel = document.getElementById('document-total-size');
        const maxFileSize = 10 * 1024 * 1024;
        const maxTotalSize = 70 * 1024 * 1024;
        const draftKey = 'tourism-provider-registration-draft';
        const uppercaseFields = new Set([
            'provider_type_other', 'commercial_name', 'business_name', 'nit',
            'legal_representative', 'identity_document', 'department', 'municipality',
            'address', 'lodging_type_other', 'lodging_services_other',
            'agency_services_other', 'package_types', 'main_destinations',
            'language_other', 'specialty_other', 'applicant_name', 'application_place',
        ]);

        form.addEventListener('input', function (event) {
            const field = event.target;
            if (!uppercaseFields.has(field.name) || typeof field.value !== 'string') return;
            const start = field.selectionStart;
            const end = field.selectionEnd;
            field.value = field.value.toLocaleUpperCase('es');
            if (typeof field.setSelectionRange === 'function' && start !== null) field.setSelectionRange(start, end);
        });

        form.addEventListener('input', function (event) {
            event.target.removeAttribute?.('aria-invalid');
            if (!form.querySelector('[aria-invalid="true"]')) {
                document.getElementById('wizard-validation-error')?.classList.add('hidden');
            }
        });

        function clearSensitiveForm() {
            localStorage.removeItem(draftKey);
            sessionStorage.removeItem(draftKey);
            form.reset();
            form.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([type="submit"]):not([type="button"]), select, textarea').forEach(function (field) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = false;
                } else if (field.tagName === 'SELECT') {
                    field.selectedIndex = 0;
                } else {
                    field.value = '';
                }
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));
            });
            inputs.forEach(function (input) {
                input.value = '';
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        function validateDocuments() {
            let total = 0;
            const oversized = [];

            inputs.forEach(function (input) {
                const file = input.files && input.files[0];
                const card = input.closest('[data-document-upload]');
                card?.classList.remove('has-error');
                if (!file) return;
                total += file.size;
                if (file.size > maxFileSize) {
                    oversized.push(file.name);
                    card?.classList.add('has-error');
                }
            });

            if (totalSizeLabel) totalSizeLabel.textContent = (total / 1024 / 1024).toFixed(total ? 1 : 0) + ' MB de 70 MB';

            let message = '';
            if (oversized.length) {
                message = 'Estos archivos superan 10 MB: ' + oversized.join(', ') + '. Comprímalos o seleccione archivos más pequeños.';
            } else if (total > maxTotalSize) {
                message = 'Los documentos suman ' + (total / 1024 / 1024).toFixed(1) + ' MB. El máximo total permitido es 70 MB.';
            }

            errorBox.textContent = message;
            errorBox.classList.toggle('hidden', !message);
            return !message;
        }

        inputs.forEach(function (input) {
            input.addEventListener('change', function () {
                const card = input.closest('[data-document-upload]');
                const file = input.files && input.files[0];
                const name = card?.querySelector('[data-file-name]');
                const size = card?.querySelector('[data-file-size]');
                const buttonText = card?.querySelector('.document-upload-button span');
                const remove = card?.querySelector('[data-remove-file]');

                card?.classList.toggle('has-file', Boolean(file));
                remove?.classList.toggle('hidden', !file);
                if (name) name.textContent = file ? file.name : 'Ningún archivo seleccionado';
                if (size) size.textContent = file ? (file.size / 1024 / 1024).toFixed(2) + ' MB' : 'PDF o imagen · máximo 10 MB';
                if (buttonText) buttonText.textContent = file ? 'Cambiar archivo' : 'Seleccionar archivo';
                validateDocuments();
            });
        });

        document.querySelectorAll('[data-remove-file]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = button.closest('[data-document-upload]')?.querySelector('.provider-document');
                if (!input) return;
                input.value = '';
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        localStorage.removeItem(draftKey);
        sessionStorage.removeItem(draftKey);

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') clearSensitiveForm();
        });
        window.addEventListener('pagehide', clearSensitiveForm);
        window.addEventListener('beforeunload', clearSensitiveForm);
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) clearSensitiveForm();
        });

        form.addEventListener('submit', function (event) {
            if (!validateDocuments()) {
                event.preventDefault();
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            const invalid = form.querySelector(':invalid');
            if (invalid) {
                event.preventDefault();
                const panel = invalid.closest('[data-provider-step]');
                const step = Number(panel?.dataset.providerStep || 1);
                window.dispatchEvent(new CustomEvent('provider-step', { detail: step }));
                setTimeout(function () {
                    validateProviderStep(step);
                }, 80);
            }
        });
    });
</script>
@endsection
