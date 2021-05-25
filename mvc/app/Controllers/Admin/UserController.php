<?php

namespace App\Controllers\Admin;

use App\Models\File;
use App\Models\User;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class UserController
 *
 * @package App\Controllers\Admin
 */
class UserController
{

    /**
     * Wir haben die Möglichkeit die Prüfung, ob ein*e User*in eingeloggt ist und die richtigen Berechtigungen hat, auch
     * hier im Konstruktor anzugeben. Nachdem der Konstruktur ausgeführt wird, sobald die Klasse instanziiert wird, wird
     * er auch vor allen anderen Methoden ausgeführt, wodurch wir alle Methoden in dem Controller "schützen" können,
     * ohne in jeder einzelnen Methode wieder die Middleware aufrufen zu müssen.
     */
    public function __construct ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();
    }

    /**
     * Liste aller User*innen im Backend ausgeben.
     */
    public function index ()
    {
        /**
         * Alle User*innen aus der Datenbank laden.
         */
        $users = User::all();

        /**
         * View laden und Daten übergeben.
         */
        View::render('admin/users/index', [
            'users' => $users
        ], 'sidebar');
    }

    /**
     * User*innen Bearbeitungsformular anzeigen.
     *
     * @param int $id
     */
    public function edit (int $id)
    {
        /**
         * User*in, die/der bearbeitet werden soll, aus der Datenbank abfragen. Wird der Eintrag nicht gefunden, wird
         * ein Fehler 404 zurückgegeben.
         */
        $user = User::findOrFail($id);

        /**
         * View laden und Daten übergeben.
         */
        View::render('admin/users/edit', [
            'user' => $user
        ], 'sidebar');
    }

    /**
     * User*in mit den Daten aus dem Bearbeitungsformular aktualisieren.
     *
     * @param int $id
     *
     * @throws \Exception
     */
    public function update (int $id)
    {
        /**
         * Nachdem wir exakt die selben Validierungen durchführen für update und create, können wir sie in eine eigene
         * Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         */
        $errors = self::validateFormData();

        /**
         * Gewünschte*n User*in über das User-Model aus der Datenbank laden. Hier verwenden wir die findOrFail()-Methode aus
         * dem AbstractModel, die einen 404 Fehler ausgibt, wenn das Objekt nicht gefunden wird. Dadurch sparen wir uns
         * hier zu prüfen ob ein Eintrag gefunden wurde oder nicht.
         */
        $user = User::findOrFail($id);

        /**
         * Nachdem die Schritte zur Verarbeitung und Speicherung von Avatar Bildern immer völlig ident sind, können wir
         * sie in eine eigene Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         */
        $errors = self::handleAvatarUpload($user, $errors);

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session um sie in den Views dann ausgeben zu können.
             */
            Session::set('errors', $errors);
        } else {
            /**
             * Sind keine Fehler aufgetreten legen aktualisieren wir die Werte des vorher geladenen Objekts ...
             */
            $user->email = trim($_POST['email']);
            /**
             * Wurde ein Username über das Formular geschickt, trimmen wir ihn und überschreiben den aktuellen Wert.
             */
            if (!empty($_POST['username'])) {
                $user->username = trim($_POST['username']);
            }
            /**
             * Wurde ein Passwort geschickt, so setzen wir es neu. Dadurch wird ein Hash erstellt und dieser dann in die
             * Datenbank gespeichert.
             */
            if (!empty($_POST['password'])) {
                $user->setPassword($_POST['password']);
            }
            /**
             * Bearbeiten wir uns gerade nicht selber?
             */
            if (User::getLoggedIn()->id !== $user->id) {
                /**
                 * Wenn wir uns nicht selbst bearbeiten, dann prüfen wir, ob die is_admin Checkbox geklickt worden ist,
                 * und wenn ja, dann vergeben wir Admin Berechtigungen oder entfernen sie, wenn die Checkbox nicht
                 * ausgewählt war.
                 */
                if (isset($_POST['is_admin']) && $_POST['is_admin'] === 'on') {
                    $user->is_admin = true;
                } else {
                    $user->is_admin = false;
                }
            }
            $user->save();

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);
        }

        /**
         * ... und leiten in jedem Fall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit Erfolgsmeldung.
         */
        Redirector::redirect(BASE_URL . "/admin/users/{$user->id}/edit");
    }

    /**
     * Abfrage, ob das Objekt wirklich gelöscht werden soll.
     *
     * @param int $id
     */
    public function deleteConfirm (int $id)
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $user = User::findOrFail($id);

        /**
         * View laden und relativ viele Daten übergeben. Die große Anzahl an Daten entsteht dadurch, dass der
         * admin/confirm-View so dynamisch wie möglich sein soll, damit wir ihn für jede Delete Confirmation Seite
         * verwenden können, unabhängig vom Objekt, das gelöscht werden soll. Wir übergeben daher einen Typ und einen
         * Titel, die für den Text der Confirmation verwendet werden, und zwei URLs, eine für den Bestätigungsbutton und
         * eine für den Abbrechen-Button.
         */
        View::render('admin/confirm', [
            'type' => 'User',
            'title' => $user->email,
            'confirmUrl' => BASE_URL . "/admin/users/{$user->id}/delete/confirm",
            'abortUrl' => BASE_URL . "/admin/users"
        ], 'sidebar');
    }

    /**
     * Objekt wirklich löschen.
     *
     * @param int $id
     */
    public function delete (int $id)
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $user = User::findOrFail($id);

        /**
         * Category löschen. Je nachdem ob das Objekt, das gelöscht wird, den SoftDelete-Trait verwendet oder nicht,
         * wird das Objekt wirklich komplett aus der Datenbank gelöscht oder eben nur auf deleted gesetzt.
         */
        $user->delete();

        /**
         * Zur Kategorie-Liste zurück leiten.
         */
        Redirector::redirect(BASE_URL . '/admin/users');
    }

    /**
     * Formular für neues Element anzeigen.
     */
    public function new ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * View laden.
         */
        View::render('admin/users/new', layout: 'sidebar');
    }

    /**
     * Daten aus dem Formular für neue Elemente entgegennehmen.
     *
     * @throws \Exception
     */
    public function create ()
    {
        /**
         * Nachdem wir exakt die selben Validierungen durchführen für update und create, können wir sie in eine eigene
         * Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         *
         * Hier setzen wir den Parameter "forcePasswordValidate" auf true, damit die Passwörter immer validiert werden,
         * auch wenn keines vom Formular übergeben wurde.
         */
        $errors = self::validateFormData(true);

        $user = new User();
        $errors = self::handleAvatarUpload($user, $errors);

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session um sie in den Views dann ausgeben zu können.
             */
            Session::set('errors', $errors);

            /**
             * Im Fehlerfall leiten wir zurück zum Formular und geben die Fehlermeldungen aus.
             */
            Redirector::redirect(BASE_URL . "/admin/users/new");
        } else {
            /**
             * Sind keine Fehler aufgetreten legen aktualisieren wir die Werte des vorher geladenen Objekts ...
             */
            $user->email = trim($_POST['email']);
            /**
             * Wurde ein Username über das Formular geschickt, trimmen wir ihn und überschreiben den aktuellen Wert.
             */
            if (!empty($_POST['username'])) {
                $user->username = trim($_POST['username']);
            }
            /**
             * Hier setzen wir ein neues Passwort. Dadurch wird ein Hash erstellt und dieser dann in die Datenbank
             * gespeichert.
             */
            $user->setPassword($_POST['password']);
            /**
             * Nachdem wir einen neuen Account anlegen, können wir hier auch Admin-Berechtigungen vergeben.
             */
            if (isset($_POST['is_admin']) && $_POST['is_admin'] === 'on') {
                $user->is_admin = true;
            }
            $user->save();

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);

            /**
             * ... und leiten im Fehlerfall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit
             * Erfolgsmeldung.
             */
            Redirector::redirect(BASE_URL . "/admin/users/{$user->id}/edit");
        }
    }

    /**
     * Validierungen kapseln, damit wir sie überall dort, wo wir derartige Objekte validieren müssen, verwenden können.
     *
     * @param bool $forcePasswordValidate
     *
     * @return array
     */
    public static function validateFormData (bool $forcePasswordValidate = false): array
    {
        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->email($_POST['email'], 'E-Mail', true);
        $validator->textnum($_POST['username'], 'Username');

        /**
         * Das Passwort soll nur dann aktualisiert werden, wenn eines in das Formular eingegeben wurde. Wir müssen es
         * also auch nur dann validieren.
         */
        if (!empty($_POST['password']) || $forcePasswordValidate === true) {
            $validator->password($_POST['password'], 'Passwort');
            /**
             * Das Feld 'password_repeat' braucht nicht validiert werden, weil wenn 'password' ein valides Passwort ist und
             * alle Kriterien erfüllt, und wir hier nun prüfen, ob 'password' und 'password_repeat' ident sind, dann ergibt
             * sich daraus, dass auch 'password_repeat' ein valides Passwort ist.
             */
            $validator->compare([
                $_POST['password'],
                'Password'
            ], [
                $_POST['password_repeat'],
                'Password wiederholen'
            ]);
        }
        /**
         * War die is_admin Checkbox nicht disabled oder aktiviert, so validieren wir sie hier auch.
         */
        if (isset($_POST['is_admin'])) {
            $validator->checkbox($_POST['is_admin'], 'Is Admin?');
        }

        /**
         * Fehler aus dem Validator holen und zurückgeben.
         */
        return $validator->getErrors();
    }

    /**
     * Avatar Upload kapseln, damit wir sie überall dort, wo wir derartige Objekte validieren müssen, verwenden können.
     *
     * @param User  $user
     * @param array $errors
     *
     * @return array
     * @throws \Exception
     */
    public static function handleAvatarUpload (User $user, array $errors = []): array
    {
        if (!empty($_FILES['avatar']['name'])) {
            /**
             * Nun kümmern wir uns um die Dateiuploads des Avatar Bildes. Die Daten zu den hochgeladenen Dateien befinden
             * sich in der $_FILES Superglobal. Damit wir eine Abstraktionsebene einziehen, bauen wir die ganze Logik für
             * den Upload und die Prüfung und Speicherung der hochgeladenen Dateien in eine eigene File-Klasse.
             */
            $avatar = new File();
            /**
             * Wir befüllen das File Objekt mit den Daten aus der $_FILES Superglobal.
             */
            $avatar->fillUploadedData($_FILES['avatar']);
            /**
             * Ist ein Upload Fehler aufgetreten oder konnte die Datei nicht korrekt validiert werden (Upload Limit,
             * Bildgröße), so schreiben wir einen Fehler.
             */
            if ($avatar->hasUploadError() || !$avatar->validateAvatar()) {
                /**
                 * Ist ein Fehler aufgetreten, schreiben wir einen Error.
                 */
                $errors[] = 'Der Dateiupload für den Avatar hat nicht funktioniert :(';
                /**
                 * Wir holen die Validierungsfehler aus dem File Objekt und fügen sie in unseren $errors Array ein.
                 */
                $errors = array_merge($errors, $avatar->getErrors());
            } else {
                /**
                 * Sind keineFehler aufgetreten, speichern wir die Datei an den angegebenen Pfad. Dabei wird auch ein
                 * Eintrag in der Datenbank angelegt, der eine ID erhält.
                 */
                $avatar->putTo('/uploads/avatars');
                /**
                 * Die soeben erstelle ID aus der Datenbank, fügen wir im $user hinzu und stellen somit die Verknüpfung von
                 * User zu Avatar-File her.
                 */
                $user->avatar = $avatar->id;
            }
        }

        return $errors;
    }

}
