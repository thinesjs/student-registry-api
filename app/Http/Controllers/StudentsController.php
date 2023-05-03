<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\StudentsResource;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Database\QueryException;
use ErrorException;
use Auth;
use Exception;
class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        if(!isset($request->name) && !isset($request->email)){
            $students = Student::paginate(5);
            return response()->json(['status' => 'success', 'data' => StudentsResource::collection($students)->response()->getData(true)], 200);
        }elseif(isset($request->name)){
            $students = Student::where('name', $request->name)->get();
            if($students == "[]") return response()->json(['status' => 'success', 'data' =>'no records']); else; return response()->json(['status' => 'success', 'query' => $request->name,'data' => StudentsResource::collection($students)], 200);
        }elseif(isset($request->email)){
            $students = Student::where('email', $request->email)->get();
            if(is_null($students)) return response()->json(['status' => 'success', 'data' =>'no records']); else; return response()->json(['status' => 'success', 'query' => $request->email,'data' => StudentsResource::collection($students)], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        if(isset($request->file)){

            try{
                Excel::import(new StudentImport, request()->file('file'));
                return response()->json(['status' => 'success', 'data' => "import success" ], 200);
            }catch(ErrorException $ex){
                return response()->json(['status' => 'error', 'message' => "import failed" ], 422);
            }catch(NoTypeDetectedException $ex){
                return response()->json(['status' => 'error', 'message' => "unrecognized file type" ], 422);
            }catch(QueryException $ex){
                return response()->json(['status' => 'error', 'message' => "possible duplicate entry" ], 422);
            }
        }
        $input = $request->all();
        $validate = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'assigned_course' => 'required'
        ]);

        if($validate->fails()) return response()->json(['status' => 'error', 'message' => $validate->errors()], 422);

        $newStudent = Student::create($input);
        return response()->json(['status' => 'success', 'data' => new StudentsResource($newStudent)], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $students = Student::find($id);
        if(is_null($students)) return response()->json(['status' => 'success', 'data' =>'no student found by the id'], 422); else return response()->json(['status' => 'success', 'data' => new StudentsResource($students)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $input = $request->all();
        $validate = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'assigned_course' => 'required'
        ]);

        if($validate->fails()) return response()->json(['status' => 'error', 'message' => $validate->errors()], 422);

        $student->name = $request->name;
        $student->email = $request->email;
        $student->address = $request->address;
        $student->assigned_course = $request->assigned_course;
        
        $student->save();

        if(is_null($student)) return response()->json(['status' => 'success', 'data' =>'no student found by the id'], 422); else return response()->json(['status' => 'success', 'data' => new StudentsResource($student)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Student::find($id);
        if($user){
            $destroy = Student::destroy($id);
            return response()->json(['status' => 'success', 'data' => 'student deleted'], 200);
        }else{
            return response()->json(['status' => 'success', 'data' => 'no student found by the id'], 422);
        }
    }
}
