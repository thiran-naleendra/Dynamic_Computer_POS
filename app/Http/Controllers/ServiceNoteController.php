<?php

namespace App\Http\Controllers;

use App\Models\ServiceNote;
use Illuminate\Http\Request;

class ServiceNoteController extends Controller
{
    private function generateServiceNo(): string
    {
        $last = ServiceNote::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;
        return 'SN-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $notes = ServiceNote::latest()->get(); // for DataTables use get()
        return view('service_notes.index', compact('notes'));
    }

    public function create()
    {
        $serviceNo = $this->generateServiceNo();
        return view('service_notes.create', compact('serviceNo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_date' => 'nullable|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_tel' => 'nullable|string|max:50',

            'item' => 'nullable|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'invoice_no' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:255',

            'customer_complains' => 'nullable|string',

            'received_service_item' => 'nullable|string|max:255',
            'grn_customer_name' => 'nullable|string|max:255',
            'grn_date' => 'nullable|date',
        ]);

        $data['service_no'] = $this->generateServiceNo();

        $note = ServiceNote::create($data);

        return redirect()->route('service-notes.show', $note)
            ->with('success', 'Service Note created successfully!');
    }

    public function show(ServiceNote $service_note)
    {
        return view('service_notes.show', ['note' => $service_note]);
    }

    public function edit(ServiceNote $service_note)
    {
        return view('service_notes.edit', ['note' => $service_note]);
    }

    public function update(Request $request, ServiceNote $service_note)
    {
        $data = $request->validate([
            'service_date' => 'nullable|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_tel' => 'nullable|string|max:50',

            'item' => 'nullable|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'invoice_no' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:255',

            'customer_complains' => 'nullable|string',

            'received_service_item' => 'nullable|string|max:255',
            'grn_customer_name' => 'nullable|string|max:255',
            'grn_date' => 'nullable|date',
        ]);

        $service_note->update($data);

        return redirect()->route('service-notes.show', $service_note)
            ->with('success', 'Service Note updated!');
    }

    public function print(ServiceNote $service_note)
    {
        return view('service_notes.print', ['note' => $service_note]);
    }

    public function destroy(ServiceNote $service_note)
    {
        $service_note->delete();
        return redirect()->route('service-notes.index')->with('success', 'Deleted!');
    }
}
