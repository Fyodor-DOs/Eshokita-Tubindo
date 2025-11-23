<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Require login first; assume AuthFilter runs as well, but double-check
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Allowed roles passed in arguments, e.g., ['admin','produksi']
        $allowed = array_map('strtolower', (array)($arguments ?? []));

        // If no arguments provided, allow all (no-op)
        if (empty($allowed)) {
            return;
        }

        $user = (array) session()->get('user');
        $role = strtolower($user['role'] ?? '');

        // Super-admin and admin bypass if included in configuration
        if (in_array('admin', $allowed, true) && $role === 'admin') {
            return;
        }
        if (in_array('super-admin', $allowed, true) && $role === 'super-admin') {
            return;
        }

        if (!in_array($role, $allowed, true)) {
            // Return 403 for AJAX/JSON calls, else redirect to home with flash
            $accept = $request->getHeaderLine('Accept');
            $xrw = $request->getHeaderLine('X-Requested-With');
            $isAjax = is_string($xrw) && strtolower($xrw) === 'xmlhttprequest';
            if ($isAjax || (is_string($accept) && stripos($accept, 'application/json') !== false)) {
                $response = service('response');
                return $response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak: peran tidak memiliki izin untuk halaman ini.'
                ]);
            }
            session()->setFlashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman tersebut.');
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No-op
    }
}
