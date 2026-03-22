<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VegetableSeedlingsDispersalSeeder extends Seeder
{
    private array $seedlingData = [
        ['date' => '08/17/2025', 'name' => 'Ellena Expedita', 'barangay' => 'Rosario', 'contact' => '09234180465', 'seedlings' => 'Okra - 3', 'control_no' => '2025-00114'],
        ['date' => '08/19/2025', 'name' => 'Alermeno', 'barangay' => 'San Vicente', 'contact' => null, 'seedlings' => 'Talong - 2, Okra - 2, Labuyo - 2', 'control_no' => '2025-00125'],
        ['date' => '08/22/2025', 'name' => 'Waind', 'barangay' => 'San Antonio', 'contact' => null, 'seedlings' => 'Talong - 3, Okra - 5', 'control_no' => '2025-00126'],
        ['date' => '08/22/2025', 'name' => 'Manuel Ellera', 'barangay' => 'Rosario', 'contact' => '09294949376', 'seedlings' => 'Sili - 5, Talong - 5', 'control_no' => '2025-00127'],
        ['date' => '09/04/2025', 'name' => 'Mike Ely', 'barangay' => null, 'contact' => '09970774589', 'seedlings' => 'Sili - 4, Talong - 4, Okra - 4, Ampalaya - 4', 'control_no' => '2025-00128'],
        ['date' => '09/05/2025', 'name' => 'Jimactal', 'barangay' => 'Rosario', 'contact' => '0917657128', 'seedlings' => 'Labuyo - 2, Talong - 2, Okra - 2, Ampalaya - 2', 'control_no' => '2025-00129'],
        ['date' => '09/08/2025', 'name' => 'R. Sanchez', 'barangay' => 'Pacita 2', 'contact' => '09158318123', 'seedlings' => 'Talong - 5', 'control_no' => '2025-00130'],
        ['date' => '09/09/2025', 'name' => 'Cristina Q. Gonzales', 'barangay' => 'Rosario', 'contact' => '094789679', 'seedlings' => 'Okra - 3', 'control_no' => '2025-00131'],
        ['date' => '09/09/2025', 'name' => 'Johanns Ruiz', 'barangay' => 'Rosario', 'contact' => '09177044067', 'seedlings' => 'Cacao - 3', 'control_no' => '2025-00132'],
        ['date' => '09/09/2025', 'name' => 'Merry Jane G. Aberia', 'barangay' => 'Nueva', 'contact' => '09481095392', 'seedlings' => 'Sili Labuyo - 3, Talong - 5, Okra - 5', 'control_no' => '2025-00132'],
        ['date' => '09/10/2025', 'name' => 'Eyarno C. Dajito', 'barangay' => 'Fatima', 'contact' => '0995893226', 'seedlings' => null, 'control_no' => '2025-00131'],
        ['date' => '09/12/2025', 'name' => 'Duay Anough', 'barangay' => 'Pacita 1', 'contact' => '09235446450', 'seedlings' => 'Talong - 5, Okra - 5', 'control_no' => '2025-00132'],
        ['date' => '09/19/2025', 'name' => 'Raul N. Santos', 'barangay' => 'Pacita 1', 'contact' => '09151801135', 'seedlings' => 'Talong - 5, Okra - 5, Sili Panigang - 5', 'control_no' => '2025-00133'],
        ['date' => '09/24/2025', 'name' => 'Marivic Catalan', 'barangay' => 'San Roque', 'contact' => '09702716168', 'seedlings' => 'Okra - 5, Talong - 5', 'control_no' => '2025-00134'],
        ['date' => '10/01/2025', 'name' => 'Bernadette Morcilla', 'barangay' => 'Landayan', 'contact' => '09512436878', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00135'],
        ['date' => '10/01/2025', 'name' => 'Mirasol D. Villena', 'barangay' => 'Laram', 'contact' => '09994736479', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00136'],
        ['date' => '10/01/2025', 'name' => 'Concepcion Sta. Ana', 'barangay' => 'San Antonio', 'contact' => '09302226742', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00137'],
        ['date' => '10/01/2025', 'name' => 'Marianne Olivarez', 'barangay' => 'Chrysanthemum', 'contact' => '09197220561', 'seedlings' => 'Talong - 3, Okra - 3', 'control_no' => '2025-00138'],
        ['date' => '10/01/2025', 'name' => 'Nesus Arn', 'barangay' => 'Maharlika', 'contact' => '09938327110', 'seedlings' => 'Talong - 3, Okra - 3', 'control_no' => '2025-00139'],
        ['date' => '10/02/2025', 'name' => 'Ermes Todoncicla', 'barangay' => 'Narra', 'contact' => '09204841170', 'seedlings' => 'Okra - 5, Talong - 5, Sili Panigang - 5', 'control_no' => '2025-00140'],
        ['date' => '10/03/2025', 'name' => 'Aster Bedico', 'barangay' => 'Poblacion', 'contact' => '09199769845', 'seedlings' => 'Talong - 5, Okra - 5', 'control_no' => '2025-00141'],
        ['date' => '10/06/2025', 'name' => 'Meu Daz', 'barangay' => 'Landayan', 'contact' => '09155124882', 'seedlings' => 'Talong - 5, Okra - 5', 'control_no' => '2025-00142'],
        ['date' => '10/08/2025', 'name' => 'Rosato Palloz', 'barangay' => 'Rosario', 'contact' => '0917259414', 'seedlings' => 'Guyabano - 3, Talong - 2', 'control_no' => '2025-00143'],
        ['date' => '10/09/2025', 'name' => 'Tidoday Paquinoas', 'barangay' => 'Rosario', 'contact' => '09194873954', 'seedlings' => 'Okra - 3, Talong - 3, Sili Panigang - 3', 'control_no' => '2025-00144'],
        ['date' => '10/10/2025', 'name' => 'Aurora G. Esmasin', 'barangay' => 'Magsaysay', 'contact' => '09100494683', 'seedlings' => 'Okra - 3, Talong - 3, Sili Panigang - 3', 'control_no' => '2025-00145'],
        ['date' => '10/10/2025', 'name' => 'Imelda Padua', 'barangay' => 'Magsaysay', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3, Sili Panigang - 3', 'control_no' => '2025-00146'],
        ['date' => '10/10/2025', 'name' => 'Bong Babon', 'barangay' => 'Magsaysay', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3, Sili Panigang - 3', 'control_no' => '2025-00147'],
        ['date' => '10/16/2025', 'name' => 'Raul N. Santos', 'barangay' => 'Pacita 1', 'contact' => '09151801135', 'seedlings' => 'Okra - 3, Talong - 3, Sili Panigang - 3', 'control_no' => '2025-00148'],
        ['date' => '10/17/2025', 'name' => 'Renari Capa', 'barangay' => 'Cuyab', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3, Sili Panigang - 3', 'control_no' => '2025-00149'],
        ['date' => '10/17/2025', 'name' => 'Lina Del Rosario', 'barangay' => 'Maharlika', 'contact' => '09173022213', 'seedlings' => 'Talong - 7, Okra - 7', 'control_no' => '2025-00150'],
        ['date' => '10/17/2025', 'name' => 'Maricez Belacaut', 'barangay' => 'Estrella', 'contact' => '09666459467', 'seedlings' => 'Okra - 1', 'control_no' => '2025-00151'],
        ['date' => '10/20/2025', 'name' => 'Ma. Trina Cruz', 'barangay' => 'Pacita 2', 'contact' => '09092350563', 'seedlings' => 'Talong - 2, Okra - 2, Sili - 2', 'control_no' => '2025-00152'],
        ['date' => '10/20/2025', 'name' => 'Nicole Lagrimas', 'barangay' => 'Cuyab', 'contact' => '09353280095', 'seedlings' => 'Talong - 2, Okra - 2, Sili - 2', 'control_no' => '2025-00153'],
        ['date' => '10/27/2025', 'name' => 'Napoleon Sta. Maria', 'barangay' => 'Cuyab', 'contact' => '09942497516', 'seedlings' => 'Sampaguita - 2', 'control_no' => '2025-00150'],
        ['date' => '10/27/2025', 'name' => 'Patria Tealorna', 'barangay' => 'Cuyab', 'contact' => '09071080724', 'seedlings' => 'Sampaguita - 2', 'control_no' => '2025-00151'],
        ['date' => '10/29/2025', 'name' => 'Manvez A. Ellea', 'barangay' => 'Rosario', 'contact' => '092949375', 'seedlings' => 'Talong - 5, Okra - 5', 'control_no' => '2025-00152'],
        ['date' => '11/03/2025', 'name' => 'Lina Brl', 'barangay' => 'Maharlika', 'contact' => '09173022213', 'seedlings' => 'Talong - 5, Okra - 5', 'control_no' => '2025-00153'],
        ['date' => '11/06/2025', 'name' => 'Cherry Cordova', 'barangay' => 'San Vicente', 'contact' => '09224178817', 'seedlings' => 'Talong - 5, Okra - 5, Basil - 1', 'control_no' => '2025-00154'],
        ['date' => '11/06/2025', 'name' => 'Christina Ronas', 'barangay' => 'San Vicente', 'contact' => '09771245015', 'seedlings' => 'Talong - 5, Okra - 5, Basil - 1', 'control_no' => '2025-00155'],
        ['date' => '11/11/2025', 'name' => 'Karen Calses', 'barangay' => 'San Vicente', 'contact' => '09108272123', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00156'],
        ['date' => '11/11/2025', 'name' => 'John Siamce Mianyal', 'barangay' => 'San Vicente', 'contact' => '09108272123', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00157'],
        ['date' => '11/17/2025', 'name' => 'Michael R. Vito', 'barangay' => 'Pacita 1', 'contact' => null, 'seedlings' => 'Talong - 5, Okra - 5', 'control_no' => '2025-00159'],
        ['date' => '11/18/2025', 'name' => 'Aulu K. Viol', 'barangay' => null, 'contact' => '09279944715', 'seedlings' => 'Talong - 5, Okra - 5', 'control_no' => '2025-00158'],
        ['date' => '11/20/2025', 'name' => 'Romeo D. Anda', 'barangay' => 'Nueva', 'contact' => '09667683989', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00160'],
        ['date' => '11/20/2025', 'name' => 'Gina J. Bonse', 'barangay' => 'Maharlika', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00161'],
        ['date' => '11/20/2025', 'name' => 'Erdena Villaruel', 'barangay' => 'Maharlika', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00162'],
        ['date' => '11/20/2025', 'name' => 'Rosita Medeo', 'barangay' => 'Maharlika', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00163'],
        ['date' => '11/21/2025', 'name' => 'Grace Sungabo', 'barangay' => 'Laram', 'contact' => null, 'seedlings' => 'Okra - 5, Talong - 5, Sili - 5', 'control_no' => '2025-00164'],
        ['date' => '11/25/2025', 'name' => 'Rusiro Aucerel', 'barangay' => 'Pacita 2', 'contact' => null, 'seedlings' => 'Kamatis - 5, Okra - 5, Talong - 5', 'control_no' => '2025-00165'],
        ['date' => '11/28/2025', 'name' => 'Raul N. Santos', 'barangay' => 'Pacita 1', 'contact' => '09157801135', 'seedlings' => 'Okra - 5, Talong - 4, Kamatis - 3', 'control_no' => '2025-00166'],
        ['date' => '12/02/2025', 'name' => 'Maria Elena Fermaly', 'barangay' => 'Pacita 1', 'contact' => '09228561808', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00167'],
        ['date' => '12/03/2025', 'name' => 'Regina Salazar', 'barangay' => 'Pacita 1', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3, Kamatis - 3', 'control_no' => '2025-00168'],
        ['date' => '12/03/2025', 'name' => 'Norilyn Regalado', 'barangay' => 'Pacita 1', 'contact' => null, 'seedlings' => 'Okra - 3, Talong - 3, Kamatis - 3', 'control_no' => '2025-00169'],
        ['date' => '12/03/2025', 'name' => 'Vosaire Ramirez', 'barangay' => 'San Lorenzo Ruiz', 'contact' => '09620031978', 'seedlings' => 'Okra - 3, Talong - 3', 'control_no' => '2025-00170'],
        ['date' => '12/09/2025', 'name' => 'S. Inogi', 'barangay' => 'San Roque', 'contact' => null, 'seedlings' => 'Kalamansi - 5', 'control_no' => '2025-00171'],
        ['date' => '12/17/2025', 'name' => 'Virginia Sabocor', 'barangay' => 'San Antonio', 'contact' => '09157241949', 'seedlings' => 'Okra - 5, Talong - 5', 'control_no' => '2025-00172'],
        ['date' => '12/19/2025', 'name' => 'January Gomez', 'barangay' => 'San Antonio', 'contact' => '09178891779', 'seedlings' => 'Okra - 5, Talong - 5', 'control_no' => '2025-00173'],
        ['date' => '12/22/2025', 'name' => 'Jafelance Irsl', 'barangay' => 'San Vicente', 'contact' => '09166324871', 'seedlings' => 'Okra - 5, Talong - 5, Kamatis - 5', 'control_no' => '2025-00174'],
        ['date' => '12/22/2025', 'name' => 'Amray Santino Barrameda', 'barangay' => 'San Vicente', 'contact' => '09454550063', 'seedlings' => 'Kalamansi - 3', 'control_no' => '2025-00175'],
        ['date' => '01/05/2026', 'name' => 'Nestoiz M. Tejero', 'barangay' => 'San Vicente', 'contact' => '09192313090', 'seedlings' => 'Okra - 5, Sili - 5', 'control_no' => '2026-001'],
        ['date' => '01/06/2026', 'name' => 'Helen Merier', 'barangay' => 'Sampaguita Village', 'contact' => '09308768816', 'seedlings' => 'Talong - 5, Okra - 5, Kamatis - 5, Papaya - 2', 'control_no' => '2026-002'],
        ['date' => '01/06/2026', 'name' => 'Annio Dinida', 'barangay' => 'San Roque', 'contact' => '09758663819', 'seedlings' => 'Talong - 5, Kamatis - 5, Papaya - 2', 'control_no' => '2026-003'],
        ['date' => '01/07/2026', 'name' => 'Lineo Del Rosario', 'barangay' => 'Maharlika', 'contact' => '09173022213', 'seedlings' => 'Talong - 5, Okra - 5, Kamatis - 5', 'control_no' => '2026-004'],
        ['date' => '01/14/2026', 'name' => 'Maria Angel Tagueloel', 'barangay' => 'San Vicente', 'contact' => '09178941626', 'seedlings' => 'Okra - 5, Talong - 5, Kamatis - 5, Aloe Vera - 1', 'control_no' => '2026-005'],
        ['date' => '01/15/2026', 'name' => 'Teodoro Maherleo', 'barangay' => 'San Antonio', 'contact' => '09191790414', 'seedlings' => 'Okra - 2, Kamatis - 2, Talong - 2', 'control_no' => '2026-007'],
        ['date' => '01/19/2026', 'name' => 'Ramil Justo', 'barangay' => 'Landayan', 'contact' => null, 'seedlings' => 'Okra - 3, Pipino - 3, Talong - 3', 'control_no' => '2026-006'],
        ['date' => '01/21/2026', 'name' => 'Krizell Denis', 'barangay' => 'Rosario', 'contact' => '09303564162', 'seedlings' => 'Talong - 3, Kamatis - 3', 'control_no' => '2026-010'],
        ['date' => '01/21/2026', 'name' => 'Daisy Galicha', 'barangay' => 'San Antonio', 'contact' => '09075575888', 'seedlings' => 'Okra - 3, Talong - 3, Kamatis - 3', 'control_no' => '2026-011'],
        ['date' => '01/21/2026', 'name' => 'Romulo Galicha', 'barangay' => 'San Antonio', 'contact' => '09075575888', 'seedlings' => 'Okra - 3, Talong - 3, Kamatis - 3', 'control_no' => '2026-012'],
        ['date' => '01/21/2026', 'name' => 'Herulnia R. Gonzales', 'barangay' => 'Pacita 2', 'contact' => '09293306241', 'seedlings' => 'Okra - 3, Kamatis - 3, Talong - 3', 'control_no' => '2026-013'],
        ['date' => '01/22/2026', 'name' => 'Luz Berroya', 'barangay' => 'San Vicente', 'contact' => '09772734414', 'seedlings' => 'Okra - 2, Kamatis - 2, Talong - 2', 'control_no' => '2026-008'],
        ['date' => '01/22/2026', 'name' => 'Orkando Reroya', 'barangay' => 'San Vicente', 'contact' => '09162361643', 'seedlings' => 'Okra - 2, Talong - 2, Kamatis - 2', 'control_no' => '2026-009'],
        ['date' => '01/22/2026', 'name' => 'Lourdes C. Estonio', 'barangay' => 'Laram', 'contact' => '09940048812', 'seedlings' => 'Kamatis - 3, Okra - 3, Talong - 3', 'control_no' => '2026-014'],
        ['date' => '01/23/2026', 'name' => 'Cesar H. Lapira', 'barangay' => 'Fatima', 'contact' => null, 'seedlings' => 'Okra - 3, Pipino - 4', 'control_no' => '2026-015'],
        ['date' => '01/23/2026', 'name' => 'Raul N. Santos', 'barangay' => 'Pacita 1', 'contact' => '09151801135', 'seedlings' => 'Okra - 5, Talong - 5, Upo - 5', 'control_no' => '2026-016'],
        ['date' => '01/28/2026', 'name' => 'Milda C. Velasco', 'barangay' => 'San Antonio', 'contact' => '09199789874', 'seedlings' => 'Okra - 2, Talong - 2, Upo - 2, Pipino - 2, Sili - 2', 'control_no' => '2026-017'],
        ['date' => '01/28/2026', 'name' => 'Hannah Meralles', 'barangay' => 'San Antonio', 'contact' => '09199789874', 'seedlings' => 'Okra - 2, Talong - 2, Upo - 2, Pipino - 2, Sili - 2', 'control_no' => '2026-018'],
        ['date' => '01/29/2026', 'name' => 'Gatcha Alvaeg', 'barangay' => 'Pacita 1', 'contact' => '09693928752', 'seedlings' => 'Okra - 5, Talong - 5, Sili Panigang - 3, Aloe Vera - 2', 'control_no' => '2026-019'],
        ['date' => '02/01/2026', 'name' => 'Ana Tubaliales', 'barangay' => 'Pacita 1', 'contact' => '09179568426', 'seedlings' => 'Okra - 2, Talong - 2, Upo - 2, Pipino - 2, Sili Panigang - 2', 'control_no' => '2026-020'],
        ['date' => '02/02/2026', 'name' => 'Ma. Elena Febuarnde', 'barangay' => 'Pacita 1', 'contact' => '09228531808', 'seedlings' => 'Okra - 2, Talong - 2, Upo - 2, Pipino - 2, Sili Panigang - 2', 'control_no' => '2026-021'],
        ['date' => '02/02/2026', 'name' => 'Mackly Quilang', 'barangay' => 'Pacita 1', 'contact' => '09166322918', 'seedlings' => 'Kalamansi - 2, Guyabano - 2', 'control_no' => '2026-022'],
        ['date' => '02/03/2026', 'name' => 'Rizal Castro', 'barangay' => 'Magsaysay', 'contact' => '09166239872', 'seedlings' => 'Pipino - 2, Upo - 2, Sili Panigang - 2', 'control_no' => '2026-023'],
        ['date' => '02/03/2026', 'name' => 'Rachel Gestiad', 'barangay' => 'Cuyab', 'contact' => '09338676618', 'seedlings' => 'Pipino - 3, Sili Panigang - 3, Upo - 3', 'control_no' => '2026-024'],
        ['date' => '02/03/2026', 'name' => 'Siwsh Mechado', 'barangay' => 'Cuyab', 'contact' => '09930886328', 'seedlings' => 'Pipino - 3, Upo - 3, Sili Panigang - 3', 'control_no' => '2026-025'],
        ['date' => '02/04/2026', 'name' => 'Esther B. Villareal', 'barangay' => 'Maharlika', 'contact' => '09760017762', 'seedlings' => 'Pipino - 3, Okra - 3, Sili Panigang - 3, Labuyo - 3', 'control_no' => '2026-026'],
        ['date' => '02/04/2026', 'name' => 'Kira Del Rosario', 'barangay' => 'Maharlika', 'contact' => '09173022213', 'seedlings' => 'Pipino - 3, Okra - 3, Sili Panigang - 3, Upo - 3', 'control_no' => '2026-027'],
        ['date' => '02/04/2026', 'name' => 'Gina J. Bonus', 'barangay' => 'Maharlika', 'contact' => '09213532429', 'seedlings' => 'Sili Panigang - 3, Okra - 3, Labuyo - 3, Pipino - 3, Upo - 3', 'control_no' => '2026-028'],
        ['date' => '02/04/2026', 'name' => 'Rosita E. Nodelo', 'barangay' => 'Maharlika', 'contact' => null, 'seedlings' => 'Sili Panigang - 3, Okra - 3, Labuyo - 3, Pipino - 3, Upo - 3', 'control_no' => '2026-029'],
        ['date' => '02/04/2026', 'name' => 'Rjoefe Esoso', 'barangay' => 'Maharlika', 'contact' => '09765380664', 'seedlings' => 'Sili Panigang - 3, Okra - 3, Labuyo - 3, Pipino - 3, Upo - 3', 'control_no' => '2026-030'],
        ['date' => '02/04/2026', 'name' => 'Narosa Sevika', 'barangay' => 'Maharlika', 'contact' => '09065791846', 'seedlings' => 'Sili Panigang - 3, Okra - 3, Labuyo - 3, Pipino - 3, Upo - 3', 'control_no' => '2026-031'],
    ];

    public function run(): void
    {
        $this->command->info('Creating historical seedling requests from provided data...');

        // Get categories and items
        $categories = $this->getCategories();
        
        if ($categories->isEmpty()) {
            $this->command->error('Please run SuppliesSeeder first!');
            return;
        }

        $this->createHistoricalRequests($categories);

        $this->command->info('Historical seedling requests seeding completed successfully!');
    }

    private function getCategories()
    {
        return RequestCategory::with('items')->get();
    }

    private function createHistoricalRequests($categories)
    {
        $usedControlNumbers = []; // Track used control numbers to handle duplicates

        foreach ($this->seedlingData as $data) {
            try {
                // Parse date
                $createdDate = Carbon::createFromFormat('m/d/Y', $data['date']);
                
                // Parse name to get first, last, etc.
                $nameParts = $this->parseName($data['name']);

                // Parse seedlings into items
                $items = $this->parseSeedlings($data['seedlings']);

                // Skip if no items
                if (empty($items)) {
                    $this->command->warn("No items found for {$data['name']}, skipping...");
                    continue;
                }

                // Calculate total quantity
                $totalQuantity = array_sum(array_column($items, 'quantity'));

                // Handle duplicate control numbers
                // Format control number as REQ-YYYY-XXXXX
                $rawControlNo = $data['control_no'];
                $formatted = $this->formatControlNumber($rawControlNo);

                // Handle duplicate control numbers
                if (isset($usedControlNumbers[$formatted])) {
                    $usedControlNumbers[$formatted]++;
                    $requestNumber = $formatted . '-' . $usedControlNumbers[$formatted];
                } else {
                    $usedControlNumbers[$formatted] = 1;
                    $requestNumber = $formatted;
                }

                // Calculate approval date (1-3 days after creation)
                $approvalDate = $createdDate->copy()->addDays(rand(1, 3));

                // Create the seedling request with claim date SAME AS approval date
                $request = SeedlingRequest::create([
                    'user_id' => null, // No user relationship
                    'request_number' => $requestNumber,
                    'first_name' => $nameParts['first_name'],
                    'middle_name' => $nameParts['middle_name'],
                    'last_name' => $nameParts['last_name'],
                    'extension_name' => $nameParts['extension_name'],
                    'contact_number' => $data['contact'],
                    'barangay' => $data['barangay'],
                    'total_quantity' => $totalQuantity,
                    'approved_quantity' => $totalQuantity, // All approved
                    'status' => 'approved', // All historical data are approved
                    'remarks' => 'Historical seedling request from ' . $data['date'] . ' (Original control no: ' . $data['control_no'] . ')',
                    'reviewed_by' => null, // No reviewer
                    'reviewed_at' => $approvalDate,
                    'approved_at' => $approvalDate,
                    'claimed_at' => $approvalDate, // Claimed on the same day as approval
                    'pickup_date' => $approvalDate->copy()->addDays(1), // Pickup available next day
                    'pickup_expired_at' => $approvalDate->copy()->addDays(30), // Expires in 30 days
                    'created_at' => $createdDate,
                    'updated_at' => $approvalDate, // Updated when approved/claimed
                ]);

                // Create request items
                $this->createRequestItems($request, $items, $categories);

                $this->command->info("Created request: {$requestNumber} - {$data['name']} (Claimed: {$approvalDate->format('Y-m-d')})");

            } catch (\Exception $e) {
                $this->command->error("Failed to create request for {$data['name']}: " . $e->getMessage());
            }
        }
    }

    private function formatControlNumber(string $controlNo): string
    {
        // Handles both "2025-00114" and "2026-001" formats
        if (preg_match('/^(\d{4})-0*(\d+)$/', $controlNo, $matches)) {
            $year = $matches[1];
            $seq  = str_pad($matches[2], 4, '0', STR_PAD_LEFT);
            return "REQ-{$year}-{$seq}";
        }

        // Fallback: just prepend REQ- if format is unrecognized
        return "REQ-{$controlNo}";
    }

    private function parseName($fullName)
    {
        $parts = preg_split('/\s+/', trim($fullName));
        $result = [
            'first_name' => $fullName,
            'middle_name' => null,
            'last_name' => null,
            'extension_name' => null,
        ];

        // Check for extension name (Jr., Sr., III, etc.)
        $extensions = ['Jr.', 'Sr.', 'III', 'II', 'IV'];
        $lastPart = end($parts);
        if (in_array($lastPart, $extensions)) {
            $result['extension_name'] = $lastPart;
            array_pop($parts);
        }

        // Handle names with middle initial (e.g., "Raul N. Santos")
        if (count($parts) >= 3 && strpos($parts[1], '.') !== false) {
            $result['first_name'] = $parts[0];
            $result['middle_name'] = rtrim($parts[1], '.');
            $result['last_name'] = implode(' ', array_slice($parts, 2));
        }
        // Handle names with two parts
        elseif (count($parts) == 2) {
            $result['first_name'] = $parts[0];
            $result['last_name'] = $parts[1];
        }
        // Handle names with three or more parts (assume first, middle, last)
        elseif (count($parts) >= 3) {
            $result['first_name'] = $parts[0];
            $result['middle_name'] = $parts[1];
            $result['last_name'] = implode(' ', array_slice($parts, 2));
        }

        return $result;
    }

    private function parseSeedlings($seedlingString)
    {
        if (empty($seedlingString)) {
            return [];
        }

        $items = [];
        $parts = explode(',', $seedlingString);

        foreach ($parts as $part) {
            $part = trim($part);
            if (preg_match('/(.+?)\s*-\s*(\d+)/', $part, $matches)) {
                $items[] = [
                    'name' => trim($matches[1]),
                    'quantity' => (int) $matches[2],
                ];
            }
        }

        return $items;
    }

    private function createRequestItems($request, $items, $categories)
    {
        foreach ($items as $itemData) {
            // Try to find matching category item
            $categoryItem = $this->findCategoryItem($itemData['name'], $categories);

            if (!$categoryItem) {
                Log::warning("Could not find category item for: {$itemData['name']}");
            }

            SeedlingRequestItem::create([
                'seedling_request_id' => $request->id,
                'user_id' => null, // No user relationship
                'category_id' => $categoryItem ? $categoryItem->category_id : null,
                'category_name' => $categoryItem ? $categoryItem->category->display_name : null,
                'category_icon' => $categoryItem ? $categoryItem->category->icon : null,
                'category_item_id' => $categoryItem ? $categoryItem->id : null,
                'item_name' => $itemData['name'],
                'item_unit' => 'pcs',
                'requested_quantity' => $itemData['quantity'],
                'approved_quantity' => $itemData['quantity'], // All approved
                'status' => 'approved',
                'rejection_reason' => null,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ]);
        }
    }

    private function findCategoryItem($itemName, $categories)
    {
        // Normalize item name for comparison
        $normalizedItemName = strtolower(trim($itemName));
        
        // Common name mappings to handle variations in the seedling names
        $nameMappings = [
            'sili' => 'Sili Labuyo',
            'labuyo' => 'Sili Labuyo',
            'ampalaya' => 'Ampalaya',
            'kamatis' => 'Kamatis',
            'talong' => 'Talong',
            'okra' => 'Okra',
            'pipino' => 'Pipino',
            'upo' => 'Upo',
            'papaya' => 'Papaya',
            'aloe vera' => 'Aloe Vera',
            'cacao' => 'Cacao',
            'guyabano' => 'Guyabano',
            'kalamansi' => 'Kalamansi',
            'sampaguita' => 'Sampaguita',
            'basil' => 'Basil',
            'calamansi' => 'Kalamansi',
            'sili panigang' => 'Sili Panigang',
        ];

        // Get mapped name
        $searchName = $nameMappings[$normalizedItemName] ?? $itemName;
        
        // Log what we're looking for
        Log::info("Looking for item - Original: '{$itemName}', Normalized: '{$normalizedItemName}', Mapped to: '{$searchName}'");

        // First, find the seedlings category
        $seedlingsCategory = null;
        foreach ($categories as $category) {
            if ($category->name === 'seedlings') {
                $seedlingsCategory = $category;
                break;
            }
        }

        if (!$seedlingsCategory) {
            Log::error("Seedlings category not found!");
            return null;
        }

        // Log all available items in seedlings category for debugging
        $availableItems = [];
        foreach ($seedlingsCategory->items as $item) {
            $availableItems[] = "'{$item->name}'";
        }
        Log::info("Available items in seedlings category: [" . implode(', ', $availableItems) . "]");

        // Only search in seedlings category items - case insensitive and trimmed
        foreach ($seedlingsCategory->items as $item) {
            if (strtolower(trim($item->name)) === strtolower(trim($searchName))) {
                Log::info("✓ Found match: '{$item->name}' for search '{$searchName}'");
                return $item;
            }
        }

        Log::warning("✗ No match found for: '{$searchName}'");
        return null;
    }
}