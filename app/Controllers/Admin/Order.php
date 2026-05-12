<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\UserModel;

class Order extends BaseController
{
    public function index()
    {
        $db      = \Config\Database::connect();
        $perPage = 15;
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));
        $search  = trim($this->request->getGet('search') ?? '');
        $status  = $this->request->getGet('status') ?? '';
        $dateFrom= $this->request->getGet('date_from') ?? '';
        $dateTo  = $this->request->getGet('date_to') ?? '';

        $builder = $db->table('orders o')
            ->select('o.*, u.name as user_name, u.email as user_email', false)
            ->join('users u', 'u.id = o.user_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('u.name', $search)
                ->orLike('u.email', $search)
                ->orLike('o.id', $search)
            ->groupEnd();
        }
        if ($status !== '') $builder->where('o.status', $status);
        if ($dateFrom !== '') $builder->where('DATE(o.created_at) >=', $dateFrom);
        if ($dateTo   !== '') $builder->where('DATE(o.created_at) <=', $dateTo);

        $total  = (clone $builder)->countAllResults();
        $orders = (clone $builder)->orderBy('o.id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()->getResultArray();

        return view('admin/orders/index', [
            'orders'     => $orders,
            'total'      => $total,
            'perPage'    => $perPage,
            'currentPage'=> $page,
            'totalPages' => max(1, (int)ceil($total / $perPage)),
            'search'     => $search,
            'status'     => $status,
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
        ]);
    }

    public function export()
    {
        $db      = \Config\Database::connect();
        $search  = trim($this->request->getGet('search') ?? '');
        $status  = $this->request->getGet('status') ?? '';
        $dateFrom= $this->request->getGet('date_from') ?? '';
        $dateTo  = $this->request->getGet('date_to') ?? '';

        $builder = $db->table('orders o')
            ->select('o.id, u.name as user_name, u.email, u.phone, o.total, o.status, o.payment_status, o.created_at', false)
            ->join('users u', 'u.id = o.user_id', 'left');

        if ($search  !== '') $builder->groupStart()->like('u.name', $search)->orLike('u.email', $search)->groupEnd();
        if ($status  !== '') $builder->where('o.status', $status);
        if ($dateFrom !== '') $builder->where('DATE(o.created_at) >=', $dateFrom);
        if ($dateTo  !== '') $builder->where('DATE(o.created_at) <=', $dateTo);

        $orders = $builder->orderBy('o.id', 'DESC')->get()->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pesanan');

        $headers = ['No','No. Order','Nama Pembeli','Email','No. HP','Total','Status','Pembayaran','Tanggal'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col.'1', $h);
            $sheet->getStyle($col.'1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '3C2A1E']],
            ]);
        }

        $statusLabel = ['pending'=>'Pending','processing'=>'Diproses','shipped'=>'Dikirim','selesai'=>'Selesai'];
        $payLabel    = ['unpaid'=>'Belum Dibayar','paid'=>'Lunas','failed'=>'Gagal','expired'=>'Kadaluarsa'];

        foreach ($orders as $idx => $o) {
            $r = $idx + 2;
            $sheet->setCellValue('A'.$r, $idx + 1);
            $sheet->setCellValue('B'.$r, '#'.str_pad($o['id'], 4, '0', STR_PAD_LEFT));
            $sheet->setCellValue('C'.$r, $o['user_name'] ?? '');
            $sheet->setCellValue('D'.$r, $o['email'] ?? '');
            $sheet->setCellValue('E'.$r, $o['phone'] ?? '');
            $sheet->setCellValue('F'.$r, (int)$o['total']);
            $sheet->setCellValue('G'.$r, $statusLabel[$o['status']] ?? $o['status']);
            $sheet->setCellValue('H'.$r, $payLabel[$o['payment_status'] ?? 'unpaid'] ?? '');
            $sheet->setCellValue('I'.$r, date('d/m/Y H:i', strtotime($o['created_at'])));

            $bg = $idx % 2 === 0 ? 'FFFFFF' : 'FFF8F0';
            $sheet->getStyle("A{$r}:I{$r}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($bg);
        }

        foreach (['A'=>6,'B'=>12,'C'=>22,'D'=>28,'E'=>16,'F'=>16,'G'=>12,'H'=>14,'I'=>18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = 'Pesanan_Toko_Roti_'.date('Ymd_His').'.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function detail($id)
    {
        $db    = \Config\Database::connect();
        $order = $db->table('orders')->where('id', $id)->get()->getRow();

        if (!$order) return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');

        $user = (new UserModel())->find($order->user_id);

        $items = $db->table('order_detail')
            ->select('order_detail.*, products.name, products.image, products.category')
            ->join('products', 'products.id = order_detail.product_id')
            ->where('order_id', $id)
            ->get()->getResultArray();

        return view('admin/orders/detail', [
            'order' => $order,
            'user'  => $user,
            'items' => $items,
        ]);
    }

    public function updateStatus()
    {
        $model  = new \App\Models\OrderModel();
        $id     = (int)$this->request->getPost('id');
        $status = $this->request->getPost('status');

        $allowed = ['processing', 'shipped'];
        if (!in_array($status, $allowed)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error']);
        }

        $model->update($id, ['status' => $status, 'notification_read' => 0]);
        return $this->response->setJSON(['status' => 'ok', 'new_status' => $status]);
    }
}