<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

// Helper
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();
        
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectRequest $request)
    {
        // dd($request->all());

        $data = $request->validated();

        // creo variabile per lo slug del title
        $slug = Str::slug($data['title']);

        if (array_key_exists('localimg', $data)) {
            $imgPath = Storage::put('uploads', $data['localimg']);
            $data['localimg'] = $imgPath;
        }
        
        // $newProject = Project::create($data);

        $newProject = Project::create([
            'title'=> $data['title'],
            'slug'=> $slug,
            'content'=> $data['content'],
            'date'=> $data['date'],
            'photo_link'=> $data['photo_link'],
            'localimg' => $imgPath,
        ]);

        return redirect()->route('admin.projects.show', $newProject->id)->with('status', 'Viaggio aggiunto con successo!');;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        if (array_key_exists('localimg', $data)) {
            $imgPath = Storage::put('uploads', $data['localimg']);
            $data['localimg'] = $imgPath;

            if($project->localimg){
                Storage::delete($project->localimg);
            }
        }

        // creo variabile per lo slug del title
        $data['slug'] = Str::slug($data['title']);

        $project->update($data);

        return redirect()->route('admin.projects.show', $project->id)->with('status', 'Viaggio aggiornato!');;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
        
        return redirect()->route('admin.projects.index', $project->id)->with('status', 'Viaggio eliminato con successo!');;
    }
}
