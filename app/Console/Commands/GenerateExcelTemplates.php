<?php

namespace App\Console\Commands;

use App\Exports\ProductCategoriesTemplateExport;
use App\Exports\SchoolClassesTemplateExport;
use App\Exports\SppPaymentsTemplateExport;
use App\Exports\StudentsTemplateExport;
use App\Exports\SubjectsTemplateExport;
use App\Exports\TeachersTemplateExport;
use App\Exports\UsersTemplateExport;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

#[Signature('app:generate-excel-templates')]
#[Description('Generate example import Excel templates for all modules in public/templates/')]
class GenerateExcelTemplates extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $templatesDir = public_path('templates');

        if (! is_dir($templatesDir)) {
            mkdir($templatesDir, 0755, true);
        }

        $templates = [
            'classes_import_example.xlsx' => new SchoolClassesTemplateExport,
            'teachers_import_example.xlsx' => new TeachersTemplateExport,
            'students_import_example.xlsx' => new StudentsTemplateExport,
            'subjects_import_example.xlsx' => new SubjectsTemplateExport,
            'spp_payments_import_example.xlsx' => new SppPaymentsTemplateExport,
            'users_import_example.xlsx' => new UsersTemplateExport,
            'product_categories_import_example.xlsx' => new ProductCategoriesTemplateExport,
        ];

        foreach ($templates as $filename => $export) {
            $path = $templatesDir.DIRECTORY_SEPARATOR.$filename;
            $binary = Excel::raw($export, ExcelFormat::XLSX);
            file_put_contents($path, $binary);
            $this->info("Generated: {$filename}");
        }

        $this->info('All Excel import templates generated successfully in public/templates/.');

        return Command::SUCCESS;
    }
}
