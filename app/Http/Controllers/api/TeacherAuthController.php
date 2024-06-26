<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher;
use Image;
use DB;

class TeacherAuthController extends Controller
{
    //  public function teacherAuthLogin(Request $request)
    // {

    // if (Auth::guard('teacher')->check()) {
    // // User is already authenticated via token
    // $user = Auth::guard('teacher')->user();
    // $token = $user->createToken('YourAppName')->plainTextToken;
    // $name = $user->name;

    // return response()->json([
    //         'token' => $token,
    //         'name' => $name,
    //         'message' => 'Teacher login successfully.',
    //     ]);
    // } else {
    // // Attempt to authenticate the user using email and password
    // $credentials = $request->only('email', 'password');

    // if (Auth::guard('teacher')->attempt($credentials)) {
    //     $user = Auth::guard('teacher')->user();
    //     $token = $user->createToken('AwsarClass')->plainTextToken;
    //     $name = $user->name;

    //   return response()->json([
    //         'token' => $token,
    //         'name' => $name,
    //         'message' => 'Teacher login successfully.',
    //     ]);
    // } else {
    //     return response()->json(['error' => 'Unauthorized']);
    // }
    // }
    // }

    public function teacherAuthLogin(Request $request){
     $login = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);
    try {
        $user = Teacher::whereEmail($login['email'])->first();
        if (!$user) {
          return response()->json(['status'=>false,'code'=>404,'message' => 'We could not find an account with that email address.Please check and try again.'], 404);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
        // Return error response for incorrect password
        return response()->json(['status'=>false,'code'=>401,'message' => 'The password you entered is incorrect. Please try again.'], 401);
        }

        if (!$user || !Hash::check($login['password'], $user->password)) {
            $data = 'Invalid Login Credentials';
            $code = 401;
        } else {

           $token = $user->createToken('AwsarClass')->plainTextToken;
           $code = 200;
           $imagePath = url('/Teachers/' . $user->image);

           $menuList = [
                [
                    'title' => 'Dashboard',
                    'iconStyle' => ' <i className="material-symbols-outlined">home</i>',
                    'to' => 'dashboard',
                ],
                [
                    'title' => 'Student',
                    'classsChange'=> 'mm-collapse',
                    'iconStyle' => '<i className="material-symbols-outlined">school</i>',
                            'content'=> [
                        [
                            'title'=> 'Student',
                            'to'=> 'student',					
                        ],           
                        ],
                ],
                [
                    'title' => 'Courses (Batch)',
                    'classsChange' => 'mm-collapse',
                    'iconStyle' => '<i className="merial-icons">article</i>',
                                            'content'=> [
                        [
                            'title'=> 'Batch',
                            'to'=> 'batch',					
                        ],
                        ],
                ],
               
                 [
                    'title' => 'Live Classes',
                     'classsChange' => 'mm-collapse',
                     'iconStyle' => '<i className="merial-icons">article</i>',
                        'content'=> [
                        [
                            'title'=> 'Live Classes',
                            'to'=> 'live-classes',					
                        ],
                        [
                            'title'=> 'Create Live Class',
                            'to'=> 'page-lock-screen',
                        ],
                      
           
                        ],
                ],
                [
                    'title' => 'Attendance',
                     'classsChange' => 'mm-collapse',
                     'iconStyle' => '<i className="merial-icons">article</i>',
                        'content'=> [
                        [
                            'title'=> 'Attendance',
                            'to'=> 'page-lock-screen',			 		
                        ],
                        [
                            'title'=> 'Todays Attendance',
                            'to'=> 'page-lock-screen',
                        ],
                      
           
                        ],
                ],
                 [
                    'title' => 'Study Material',
                    'classsChange' => 'mm-collapse',	
                    'iconStyle' => '<i className="material-symbols-outlined">article</i>',
                        'content'=> [
                        [
                            'title'=> 'Study Materials',
                            'to'=> 'study-materials',					
                        ],
                        [
                            'title'=> 'Upload Study Material',
                            'to'=> 'add-study-material',
                        ],
           
                        ],
                ],
                 [
                    'title' => 'Exams',
                     'classsChange' => 'mm-collapse',
                     'iconStyle' => '<i className="merial-icons">settings</i>',
                        'content'=> [
                        [
                            'title'=> 'View Exam',
                            'to'=> 'page-lock-screen',					
                        ],
                        [
                            'title'=> 'Create Exam',
                            'to'=> 'page-lock-screen',
                        ],
                      
           
                        ],
                ],
                 [
                    'title' => 'Class Routine',
                     'classsChange' => 'mm-collapse',
                     'iconStyle' => '<i className="merial-icons">settings</i>',
                        'content'=> [
                        [
                            'title'=> 'View Routine',
                            'to'=> 'page-lock-screen',					
                        ],
                        [
                            'title'=> 'Create Routine',
                            'to'=> 'page-lock-screen',
                        ],
                      
           
                        ],
                ],
                 [
                    'title' => 'Notice',
                     'classsChange' => 'mm-collapse',
                     'iconStyle' => '<i className="merial-icons">settings</i>',
                        'content'=> [
                        [
                            'title'=> 'View Notice',
                            'to'=> 'page-lock-screen',					
                        ],
                        [
                            'title'=> 'Create Notice',
                            'to'=> 'page-lock-screen',
                        ],
                      
           
                        ],
                ],
                [
                    'title' => 'Leave Request',
                    'iconStyle' => '<i className="material-icons">settings</i>',
                    'to' => 'settings',
                ],
                [
                    'title' => 'Settings',
                    'iconStyle' => '<i className="material-icons">settings</i>',
                    'to' => 'teacher/settings',
                ],
            ];

            
            $data = [
            'teacher' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'street' => $user->street,
            'postal_code' => $user->postal_code,
            'city' => $user->city,
            'state' => $user->state,
            'image' => $user->image ? url('/Teachers/' . $user->image) : null,
            'classes' => $user->classes,
            ],
                'token' => $token,
                'message' => 'Login Successfully',
                'role' => $menuList,
            ];
        }
     } catch (Exception $e) {
        $data = ['error' => $e->getMessage()];
    }
    return response()->json($data, $code);
} 

public function teacherList(){
    $teacher = Teacher::orderByDesc('id')->get();
    return response()->json(['status'=>true,'code'=>200,'data'=>$teacher]);
}

 public function UpdateView($id){
   $teacher = Teacher::find($id);
   $imagePath = url('/Teachers/' . $teacher->image);

   if($teacher){
   return response()->json(['status'=>true,'code'=>200,'data'=>$teacher,'image'=>$imagePath]);

   }else{
     return response()->json(['status'=>false,'code'=>404,'message' => 'Teacher not found'], 404);
   }
  }

    public function teacherAuthLogout(Request $request)
    {
       $admin = Auth::guard('teacher')->user();
        
        if ($admin) {
            $admin->tokens()->where('name', 'AwsarClass')->delete();
        }

        return response()->json(['status'=>true,'code'=>200,'message' => 'Successfully logged out']);
    }


    // public function teacherCreate(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:teachers',
    //         'phone' => 'required|numeric|digits:10|unique:teachers',
    //         'street' => ['nullable', 'string', 'min:1', 'max:250'], 
    //         'postal_code' => ['nullable', 'numeric', 'digits:6'],
    //         'city' => ['nullable', 'string', 'min:1', 'max:250'],
    //         'state' => ['nullable', 'string', 'min:1', 'max:250'],
    //         'classes' => 'required|array',
    //         'image' => 'nullable',
    //         'password' => 'required|string|min:6|confirmed',
    //     ]);

    //      if ($validator->fails()) {
    //         return response()->json([
    //          'status' => false,
    //            'code'=>400,
    //           'errors' => $validator->errors()
    //           ], 400);
    //     }

    //     try{
    //         if($request->image!=''){
    //        $uploadedImg=$request->image;
    //        $fileName=time().'.'.$request->image->extension();          
    //        $destinationpath=public_path('/Teachers');
    //        $img=Image::make($uploadedImg->path());     
    //        $img->resize(200,null, function($constraint){
    //        $constraint->aspectRatio();
    //        })->save($destinationpath.'/'.$fileName);
    //       }else{
    //        $fileName='';
    //       }
    //         $teacher = new Teacher();
    //         $teacher->name = $request->input('name'); 
    //         $teacher->email = $request->input('email');
    //         $teacher->phone = $request->input('phone');
    //         $teacher->street = $request->input('street');
    //         $teacher->postal_code = $request->input('postal_code');
    //         $teacher->city = $request->input('city');
    //         $teacher->state = $request->input('state');
    //         $teacher->image = $fileName;
    //         $teacher->password =Hash::make($request->password);
    //         $teacher->classes =$request->input('classes');
    //         $teacher->save();
    //         //  $imagePath = url('/Teachers/' . $teacher->image);
    //           $imagePath = $teacher->image ? url('/Teachers/' . $teacher->image) : null;

    //       return response()->json(['status'=>true,'code'=>200,'message' => 'Teacher registered successfully', 'teacher' => $teacher,'image'=>$imagePath], 200);
    //     }catch (Exception $e) {
    //      $data = ['error' => $e->getMessage()];
    //       return response()->json(['status'=>false,'code'=>500,'message' => 'An error occurred while registering Teacher', 'data' => $data], 500);

    //     }
    // }
    public function teacherCreate(Request $request)
{
    // Validate request inputs
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:teachers',
        'phone' => 'required|numeric|digits:10|unique:teachers',
        'street' => 'nullable|string|min:1|max:250', 
        'postal_code' => 'nullable|numeric|digits:6',
        'city' => 'nullable|string|min:1|max:250',
        'state' => 'nullable|string|min:1|max:250',
        'classes' => 'required|array',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'password' => 'required|string|min:6|confirmed',
    ]);

    // If validation fails, return errors
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'code' => 400,
            'errors' => $validator->errors()
        ], 400);
    }

    try {
        // Start transaction
        DB::beginTransaction();

        // Handle image upload if present
        $fileName = '';
        if ($request->hasFile('image')) {
            $uploadedImg = $request->file('image');
            $fileName = time() . '.' . $uploadedImg->getClientOriginalExtension();          
            $destinationPath = public_path('/Teachers');
            $img = Image::make($uploadedImg->path());     
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $fileName);
        }

        // Create new Teacher
        $teacher = new Teacher();
        $teacher->name = $request->input('name'); 
        $teacher->email = $request->input('email');
        $teacher->phone = $request->input('phone');
        $teacher->street = $request->input('street');
        $teacher->postal_code = $request->input('postal_code');
        $teacher->city = $request->input('city');
        $teacher->state = $request->input('state');
        $teacher->image = $fileName;
        $teacher->password = Hash::make($request->input('password'));
        $teacher->classes = $request->input('classes');
        $teacher->save();

        // Commit transaction
        DB::commit();

        $imagePath = $teacher->image ? url('/Teachers/' . $teacher->image) : null;

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Teacher registered successfully',
            'teacher' => $teacher,
            'image' => $imagePath
        ], 200);
    } catch (\Exception $e) {
        // Rollback transaction if any error occurs
        DB::rollBack();

        return response()->json([
            'status' => false,
            'code' => 500,
            'message' => 'An error occurred while registering Teacher',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
    public function updateTeacher(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers,email,' . $id,
            'phone' => 'required|numeric|digits:10|unique:teachers,phone,' . $id,
            'street' => ['nullable', 'string', 'min:1', 'max:250'], 
            'postal_code' => ['nullable', 'numeric', 'digits:6'],
            'city' => ['nullable', 'string', 'min:1', 'max:250'],
            'state' => ['nullable', 'string', 'min:1', 'max:250'],
            'classes' => 'required|array',
            'image' => 'nullable',
            // 'password' => 'required|string|min:6|confirmed',
        ]);

         if ($validator->fails()) {
            return response()->json([
             'status' => false,
               'code'=>400,
              'errors' => $validator->errors()
              ], 400);
        }

        try{
           if($request->image!=''){
           $uploadedImg=$request->image;
           $fileName=time().'.'.$request->image->extension();          
           $destinationpath=public_path('/Teachers');
           $img=Image::make($uploadedImg->path());     
           $img->resize(200,null, function($constraint){
           $constraint->aspectRatio();
           })->save($destinationpath.'/'.$fileName);
          }else{
           $fileName='';
          }
            $teacher = Teacher::find($id);
             if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
            }
            $teacher->name = $request->input('name');
            $teacher->email = $request->input('email');
            $teacher->phone = $request->input('phone');
            $teacher->street = $request->input('street');
            $teacher->postal_code = $request->input('postal_code');
            $teacher->city = $request->input('city');
            $teacher->state = $request->input('state');
            $teacher->image = $fileName;
            // $teacher->password =Hash::make($request->password);
            $teacher->classes =$request->input('classes');
            $teacher->save();
               $imagePath = $teacher->image ? url('/Teachers/' . $teacher->image) : null;
           
        return response()->json(['status'=>true,'code'=>200,'message' => 'Teacher updated successfully', 'teacher' => $teacher , 'image' =>$imagePath], 200);
         }catch (Exception $e) {
         $data = ['error' => $e->getMessage()];
          return response()->json(['status'=>false,'code'=>500,'message' => 'An error occurred while Updating Teacher', 'data' => $data], 500);

        }
    }


    public function deleteTeacher($id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json(['status'=>false,'code'=>404,'message' => 'Teacher not found'], 404);
        }

        $teacher->delete();

        return response()->json(['status'=>true,'code'=>200,'message' => 'Teacher deleted successfully'], 200);
    }

    public function profileUpdateView($id){

        $teacher = Teacher::find($id);
          $imagePath = $teacher->image ? url('/Teachers/' . $teacher->image) : null;
        if($teacher){
        return response()->json(['status'=>true,'code'=>200,'data'=>$teacher,'image'=>$imagePath]);
        }else{
        return response()->json(['status'=>false,'code'=>404,'message' => 'Teacher not found'], 404);
        }
    }

    public function profileUpdate(Request $request,$id){

       $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers,email,' . $id,
            'phone' => 'required|numeric|digits:10|unique:teachers,phone,' . $id,
            'street' => ['nullable', 'string', 'min:1', 'max:250'], 
            'postal_code' => ['nullable', 'numeric', 'digits:6'],
            'city' => ['nullable', 'string', 'min:1', 'max:250'],
            'state' => ['nullable', 'string', 'min:1', 'max:250'],
            'classes' => 'required|array',
            'image' => 'nullable',
           
        ]);

         if ($validator->fails()) {
            return response()->json([
             'status' => false,
               'code'=>400,
              'errors' => $validator->errors()
              ], 400);
        }

        try{

        if($request->image!=''){
           $uploadedImg=$request->image;
           $fileName=time().'.'.$request->image->extension();          
           $destinationpath=public_path('/Teachers');
           $img=Image::make($uploadedImg->path());     
           $img->resize(200,null, function($constraint){
           $constraint->aspectRatio();
           })->save($destinationpath.'/'.$fileName);
          }else{
           $fileName='';
          }
            $teacher = Teacher::find($id);
             if (!$teacher) {
            return response()->json(['status'=>false,'code'=>404,'message' => 'Teacher not found'], 404);
            }
            $teacher->name = $request->input('name');
            $teacher->email = $request->input('email');
            $teacher->phone = $request->input('phone');
            $teacher->street = $request->input('street');
            $teacher->postal_code = $request->input('postal_code');
            $teacher->city = $request->input('city');
            $teacher->state = $request->input('state');
            $teacher->image = $fileName;
            $teacher->classes =$request->input('classes');
            $teacher->save();
                $imagePath = $teacher->image ? url('/Teachers/' . $teacher->image) : null;
            return response()->json(['status'=>true,'code'=>200,'message' => 'Profile Updated Successfully', 'teacher' => $teacher, 'image' =>$imagePath], 200);
        }catch (Exception $e) {
            $data = ['error' => $e->getMessage()];
            return response()->json(['status'=>false,'code'=>500,'message' => 'An error occurred while updating profile', 'data' => $data], 500);
        }
    }

    public function passwordUpdate(Request $request){

        $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
        'new_password' => 'required|string',
        ]);

         if ($validator->fails()) {
            return response()->json([
             'status' => false,
               'code'=>400,
              'errors' => $validator->errors()
              ], 400);
        }
        $teacher = Teacher::where('email',$request->input('email'))->first();
        
        if($teacher){

            if (Hash::check($request->input('password'), $teacher->password)) {
                $teacher->password = Hash::make($request->new_password);
                $teacher->save();
                return response()->json(['status'=>true,'code'=>200,'message' => 'Your password has been updated successfully.'], 200);
            }else{
            return response()->json(['status'=>false,'code'=>401,'message' => 'The password you entered is incorrect'], 401);
            }
        }else{
        return response()->json(['status'=>false,'code'=>404,'message' => 'We could not find an account with that email address. Please check and try again.'], 404);
        }

        
    }
}
