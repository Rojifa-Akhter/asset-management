<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //create or add organization
    public function addOrganization(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'address'  => 'required|string',
            'documents.*' => 'nullable|file',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 401);
        }
         // Upload new documents
         $newDocuments = [];
         if ($request->hasFile('documents')) {
             foreach ($request->file('documents') as $document) {
                 $documentName = time() . uniqid() . '_' . $document->getClientOriginalName();
                 $document->move(public_path('uploads/documents'), $documentName);
                 $newDocuments[] = $documentName;
             }
         }

         $organization = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => 'organization',
            'password' => Hash::make($request->password),
            'address'  => $request->address,
            'document' => json_encode($newDocuments),
        ]);

        $organization->save();

        return response()->json(['status' => true, 'message' => 'Organization Create Successfully', 'organization' => $organization], 200);
    }
    //update organization
    public function updateOrganization(Request $request,$id)
    {
        $organization = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'address'  => 'nullable|string',
            'document' => 'nullable|file',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 401);
        }
        $validatedData = $validator->validated();
        $organization->name    = $validatedData['name'] ?? $organization->name;
        $organization->address = $validatedData['address'] ?? $organization->address;
        $organization->phone   = $validatedData['phone'] ?? $organization->phone;

        if (! empty($validatedData['password'])) {
            $organization->password = Hash::make($validatedData['password']);
        }
        if ($request->hasFile('image')) {
            $existingImage = $organization->image;

            if ($existingImage) {
                $oldImage = parse_url($existingImage);
                $filePath = ltrim($oldImage['path'], '/');
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the existing image
                }
            }

            // Upload new image
            $image     = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $newName   = time() . '.' . $extension;
            $image->move(public_path('uploads/profile_images'), $newName);

            $organization->image = $newName;
        }
        //delete old document
        if ($request->hasFile('documents')) {
            $existingDocuments = $organization->document;

            if (is_array($existingDocuments)) {
                foreach ($existingDocuments as $document) {
                    $relativePath = parse_url($document, PHP_URL_PATH);
                    $relativePath = ltrim($relativePath, '/');
                    unlink(public_path($relativePath));
                }
            }

            // Upload new documents
            $newDocuments = [];
            foreach ($request->file('documents') as $document) {
                $documentName = time() . uniqid() . $document->getClientOriginalName();
                $document->move(public_path('uploads/documents'), $documentName);

                $newDocuments[] = $documentName;
            }

            $organization->document = json_encode($newDocuments);
        }
        $organization->save();

        return response()->json(['status' => true, 'message' => 'Organization Update Successfully', 'organization' => $organization], 200);
    }
    //create or add third party
    public function addThirdParty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'address'  => 'required|string',
            'documents.*' => 'nullable|file',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 401);
        }
         // Upload new documents
         $newDocuments = [];
         if ($request->hasFile('documents')) {
             foreach ($request->file('documents') as $document) {
                 $documentName = time() . uniqid() . '_' . $document->getClientOriginalName();
                 $document->move(public_path('uploads/documents'), $documentName);
                 $newDocuments[] = $documentName;
             }
         }

         $third_party = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => 'third_party',
            'password' => Hash::make($request->password),
            'address'  => $request->address,
            'document' => json_encode($newDocuments),
        ]);

        $third_party->save();

        return response()->json(['status' => true, 'message' => 'Third Party Create Successfully', 'third_party' => $third_party], 200);
    }
    //update third party
    public function updateThirdParty(Request $request,$id)
    {
        $third_party = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'address'  => 'nullable|string',
            'document' => 'nullable|file',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 401);
        }
        $validatedData = $validator->validated();
        $third_party->name    = $validatedData['name'] ?? $third_party->name;
        $third_party->address = $validatedData['address'] ?? $third_party->address;
        $third_party->phone   = $validatedData['phone'] ?? $third_party->phone;

        if (! empty($validatedData['password'])) {
            $third_party->password = Hash::make($validatedData['password']);
        }
        if ($request->hasFile('image')) {
            $existingImage = $third_party->image;

            if ($existingImage) {
                $oldImage = parse_url($existingImage);
                $filePath = ltrim($oldImage['path'], '/');
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the existing image
                }
            }

            // Upload new image
            $image     = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $newName   = time() . '.' . $extension;
            $image->move(public_path('uploads/profile_images'), $newName);

            $third_party->image = $newName;
        }
        //delete old document
        if ($request->hasFile('documents')) {
            $existingDocuments = $third_party->document;

            if (is_array($existingDocuments)) {
                foreach ($existingDocuments as $document) {
                    $relativePath = parse_url($document, PHP_URL_PATH);
                    $relativePath = ltrim($relativePath, '/');
                    unlink(public_path($relativePath));
                }
            }

            // Upload new documents
            $newDocuments = [];
            foreach ($request->file('documents') as $document) {
                $documentName = time() . uniqid() . $document->getClientOriginalName();
                $document->move(public_path('uploads/documents'), $documentName);

                $newDocuments[] = $documentName;
            }

            $third_party->document = json_encode($newDocuments);
        }
        $third_party->save();

        return response()->json(['status' => true, 'message' => 'third_party Update Successfully', 'third_party' => $third_party], 200);
    }    public function deleteUser($id)
    {
        //  return $request;
        $user = User::find($id);

        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function SoftDeletedUsers()
    {
        $deletedUsers = User::onlyTrashed()->get();

        return response()->json(['message' => $deletedUsers], 200);
    }
}
