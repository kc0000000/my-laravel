<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator; // Add this import
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables; 


class UserController extends Controller
{
    public function index(){
        $roles = Role::all();
        return view('welcome', compact('roles'));
    }
    // Show the form
    public function showForm()
    {
        $roles = Role::all();
        return view('user-form', compact('roles'));
    }

    // Store the user data
    public function store(Request $request)
    {
        // Validate the data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // 'phone' => 'required|regex:/^(\+91|0)?[7-9][0-9]{9}$/',
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'description' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Store the profile image
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            // echo "test";exit;
            $imagePath = $request->file('profile_image')->store('/','public_uploads');
            $imageUrl = 'uploads/' . basename($imagePath);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'description' => $request->description,
            'role_id' => $request->role_id,
            'profile_image' => $imageUrl,
        ]);

        return response()->json(['success' => 'User created successfully', 'user' => $user]);
    }

    // Fetch users for the table
    public function fetchUsers()
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    public function getUsers()
    {
        $users = User::with('role')->get(); // Assuming 'role' is a relationship

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('profile_image', function($user) {
                return $user->profile_image;
            })
            ->make(true);
    }

    public function show($id)
    {
        // Retrieve user by ID from the database
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Return user data (you can customize this based on what you need)
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:15',
            'description' => 'nullable|string',
            'role_id' => 'required|integer',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        try {
            // Update user details
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->description = $request->description;
            $user->role_id = $request->role_id;

            // Handle file upload if a new image is provided
            if ($request->hasFile('profile_image')) {
                $profileImage = $request->file('profile_image');
                $path = $profileImage->store('/', 'public_uploads'); // Store in 'public/uploads'
                // $imagePath = $request->file('profile_image')->store('/','public_uploads');
                $user->profile_image = "uploads/".$path;
            }

            $user->save();

            return response()->json(['success' => 'User updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    


}
