<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\SearchAddressRequest;
use App\Models\Address;
use App\Http\Resources\V1\Address\AddressResource;
use App\Http\Resources\V1\Address\AddressCollection;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{

    public function index()
    {
        try {
            $addresses = Address::get();
            return response()->json($addresses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function searchByCep($cep)
    {
        try {
            $addresses = Address::where('cep', $cep)->get();

            if ($addresses->isEmpty()) {
                return response()->json(['error' => 'Address not found'], Response::HTTP_NOT_FOUND);
            }

            return AddressResource::collection($addresses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeAfterCepSearch(StoreAddressRequest $request)
    {
        try {

            $data = $request->validated();

            // Definir campos como nulos se estiverem vazios

            Address::create($data);
            return response()->json("Address Created", Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    public function store(StoreAddressRequest $request)
    {
        try {
            Address::create($request->validated());
            return response()->json("Address Created", Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StoreAddressRequest $request, Address $address)
    {
        try {
            $address->update($request->validated());
            return response()->json("Address Updated");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Address $address)
    {
        try {
            $address->delete();
            return response()->json("Address Deleted");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
