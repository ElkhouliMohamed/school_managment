// app/Models/User.php
<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function parent()
    {
        return $this->hasOne(Parent::class);
    }

    public function accountant()
    {
        return $this->hasOne(Accountant::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }
}


// app/Models/Student.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'date_of_birth', 'class_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function parents()
    {
        return $this->belongsToMany(Parent::class, 'parent_student');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'student_transport')
                    ->withPivot('start_date', 'end_date');
    }
}

// app/Models/Parent.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parent extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student');
    }
}


// app/Models/ClassModel.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name', 'level',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}


// app/Models/Subject.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name', 'class_id', 'teacher_id',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}


// app/Models/Absence.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'student_id', 'subject_id', 'date', 'reason',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}



// app/Models/Grade.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'student_id', 'subject_id', 'grade', 'exam_date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}



// app/Models/Payment.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id', 'amount', 'payment_date', 'payment_type', 'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}



// app/Models/Transport.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'vehicle_number', 'driver_name', 'route_description',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_transport')
                    ->withPivot('start_date', 'end_date');
    }
}

// app/Models/Accountant.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accountant extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


php artisan make:model User -m
php artisan make:model Student -m
php artisan make:model Parent -m
php artisan make:model ClassModel -m
php artisan make:model Subject -m
php artisan make:model Absence -m
php artisan make:model Grade -m
php artisan make:model Payment -m
php artisan make:model Transport -m
php artisan make:model Timetable -m
php artisan make:model Accountant -m

// routes/web.php
<?php
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\AccountantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('students', StudentController::class)->middleware('role:admin');
    Route::resource('parents', ParentController::class)->middleware('role:admin');
    Route::resource('classes', ClassController::class)->middleware('role:admin');
    Route::resource('subjects', SubjectController::class)->middleware('role:admin');
    Route::resource('absences', AbsenceController::class)->middleware('role:teacher|admin');
    Route::resource('grades', GradeController::class)->middleware('role:teacher|admin');
    Route::resource('payments', PaymentController::class)->middleware('role:accountant|admin');
    Route::resource('transports', TransportController::class)->middleware('role:admin');
    Route::resource('timetables', TimetableController::class);
    Route::get('timetables/export-pdf', [TimetableController::class, 'exportPdf'])->name('timetables.exportPdf');
    Route::resource('accountants', AccountantController::class)->middleware('role:admin');
});

require __DIR__.'/auth.php';


php artisan make:seeder RoleSeeder


// database/seeders/RoleSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'parent']);
        Role::create(['name' => 'student']);
        Role::create(['name' => 'accountant']);
    }
}

php artisan db:seed --class=RoleSeeder
