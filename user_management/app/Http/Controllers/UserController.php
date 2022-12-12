<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
    
        return view('users.edit',compact('user','roles','userRole'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    { 
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phonenumber' => 'required',
            'images' => 'mimes:jpg,png,jpeg,gif,svg'
        ]);
        $imageName = '';
            if ($request->hasFile('images')) {
            $file = $request->file('images');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move('upload/images', $imageName);
            if ($user->images) {
                Storage::delete('upload/images/' . $user->images);
            }
            } else {
            $imageName = $user->images;
            }
            if(!empty($user['password'])){ 
                $user['password'] = Hash::make($user['password']);
            }else{
                $user = Arr::except($user,array('password'));    
            }
            $userData = ['first_name' => $request->first_name,
            'last_name' => $request->last_name, 
            'email' => $request->email,
            'phonenumber' =>  $request->phonenumber,
            'city' => $request->city,
            'country' => $request->country,
            'images' => $imageName];

            $user->update($userData);
            DB::table('model_has_roles')->where('model_id',$user->id)->delete();
            $user->assignRole($request->input('roles'));
            
            return redirect()->route('users.index')
            ->with('success','User updated successfully');
    }
  
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
   
    /**
     *Import User Data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request){
        $path = $request->file('select_file')->getRealPath();
       /* Start Validation rule */
        $validator = \Validator::make(
        [
            'select_file' => $request->hasFile('select_file')? strtolower($request->file('select_file')->getClientOriginalExtension()) : null,
        ],
        [
            'select_file'      => 'required|in:csv',
        ]
        );
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
             ], 401);
                }        
                /* End Validation rule */
                $errors =  array();
                $data = array_map('str_getcsv', file($path));
                foreach($data as $key => $column_value){
                    $insertData[] = [
                        'first_name' => $column_value[0], 
                        'last_name' => $column_value[1], 
                        'email' => $column_value[2], 
                        'phonenumber' => $column_value[3], 
                        'city' =>$column_value[4],
                        'country' =>$column_value[5]];
                 
                   
                }
                  
            DB::table('users')->insert($insertData);
            if (empty($errors)) {   
            return back()->with('success', 'CSV Data Imported successfully.');
            }


    }

}