<?php

if (!function_exists('rateLabel')) {
  function rateLabel($rating)
  {
    switch ($rating) {
      case 1:
        return "Tidak Baik";
        break;

      case 2:
        return "Kurang Baik";
        break;

      case 3:
        return "Baik";
        break;

      case 4:
        return "Sangat Baik";
        break;
    }
  }
}

if (!function_exists('nilaPersepsi')) {
  function nilaPersepsi($konversiIKM)
  {
    if ($konversiIKM >= 25.00 && $konversiIKM <= 64.99) {
      return (object) [
        'mutu' => 'D',
        'kinerja' => "Tidak Baik"
      ];
    } elseif ($konversiIKM >= 65.00 && $konversiIKM <= 76.00) {
      return (object) [
        'mutu' => 'C',
        'kinerja' => "Kurang Baik"
      ];
    } elseif ($konversiIKM >= 76.01 && $konversiIKM <= 88.30) {
      return (object) [
        'mutu' => 'B',
        'kinerja' => "Baik"
      ];
    } elseif ($konversiIKM >= 88.31 && $konversiIKM <= 100.00) {
      return (object) [
        'mutu' => 'A',
        'kinerja' => "Sangat Baik"
      ];
    } else {
      return (object) [
        'mutu' => 'X',
        'kinerja' => "Nilai Invalid"
      ];
    }
  }
}

if (!function_exists('getPercentage')) {
  function getPercentage($number, $total)
  {
    if ($total == 0) {
      return 0;
    }
    return $number * 100 / $total;
  }
}

if (!function_exists('getIKM')) {
  function getIKM($respondens, $kuesioners)
  {
    $data = [];

    $bobotNilaiTertimbang = 1;
    if (count($kuesioners) > 0) {
      $bobotNilaiTertimbang = 1 / count($kuesioners);
    }

    // Hitung total nilai dan jumlah responden per kuesioner_id
    $totalNilai = [];
    $jumlahRespondenPerKuesioner = [];

    foreach ($respondens as $responden) {
      foreach ($responden->answers as $answer) {
        $kuesionerId = $answer->kuesioner_id;

        // Tambah nilai
        if (!isset($totalNilai[$kuesionerId])) {
          $totalNilai[$kuesionerId] = 0;
        }
        $totalNilai[$kuesionerId] += $answer->answer;

        // Tambah jumlah responden untuk kuesioner tersebut
        if (!isset($jumlahRespondenPerKuesioner[$kuesionerId])) {
          $jumlahRespondenPerKuesioner[$kuesionerId] = 0;
        }
        $jumlahRespondenPerKuesioner[$kuesionerId]++;
      }
    }

    foreach ($totalNilai as $kuesionerId => $total) {
      $kuesioner = $kuesioners->firstWhere('id', $kuesionerId);
      $jumlahResponden = $jumlahRespondenPerKuesioner[$kuesionerId] ?? 1;

      $NRR = $total / $jumlahResponden;
      $NRRTertimbang = $NRR * $bobotNilaiTertimbang;

      $data[$kuesionerId] = (object) [
        'question' => $kuesioner ? $kuesioner->question : 'Tidak ditemukan',
        'jumlahResponden' => $jumlahResponden,
        'totalNilaiPersepsiPerUnit' => $total,
        'NRRPerUnsur' => $NRR,
        'NRRTertimbangUnsur' => $NRRTertimbang
      ];
    }

    // Total IKM
    $IKM = array_sum(array_map(fn($item) => $item->NRRTertimbangUnsur, $data));
    $konversiIKM = $IKM * 25;

    return [
      'data' => $data,
      'IKM' => $IKM,
      'konversiIKM' => $konversiIKM,
      'bobotNilaiTertimbang' => $bobotNilaiTertimbang
    ];
  }
}


