<?php include "header.php"; include "koneksi.php"; include "tgl_indonesia.php"; ?>
            <!-- start: Content -->
            <div id="content">
               <div class="panel box-shadow-none content-header">
                  <div class="panel-body">
                    <div class="col-md-12">
                        <h3 class="animated fadeInUp text-primary">Data Pensiun Duda Janda</h3>
                        <!--<p class="animated fadeInDown">
                          Table <span class="fa-angle-right fa"></span> Data Tables
                        </p>-->
                    </div>
                  </div>
              </div>
              <div class="col-md-12 top-20 padding-0">
                <div class="col-md-12">
                  <div class="panel">
                    <div class="panel-heading">
                      <div class="row">
                        <div class="col-md-4">
                          <button type="button" class="btn btn-primary btn-round" data-toggle="modal" data-target="#modal-pensiun"><i class="fa fa-plus"></i> TAMBAH</button>
                      <a href="laporan_dj?filter=<?php echo $_GET['filter'] ?>" target="_blank" class="btn btn-round btn-warning"><i class="fa fa-print"></i> REKAP</a>
                        </div>
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                          <form method="get">
                            <select name="filter" class="form-control" onchange="this.form.submit()">
                              <option selected="" disabled="">-PILIH-</option>
                              <option value="ALL">SEMUA</option>
                              <?php
                              error_reporting(0);
                              $sql = mysqli_query($konek,"SELECT tgl_surat FROM pen_dj GROUP BY month(tgl_surat)");
                              foreach($sql as $ts){
                              $tgl = explode('-', $ts['tgl_surat']);
                              $t = $tgl[0].'-'.$tgl[1];
                              ?>
                              <option value="<?php echo $t; ?>"><?php echo tgl_indo($t); ?></option>
                              <?php } ?>
                            </select>
                          </form>
                        </div>
                      </div>
                    </div>

                    <?php
                  if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
                    ?>
                    <div id="pesan" class="alert alert-success col-md-12 col-sm-12  alert-icon alert-dismissible fade in" role="alert" style="display:none;">
                      <div class="col-md-2 col-sm-2 icon-wrapper text-center">
                        <span class="fa fa-check fa-2x"></span></div>
                        <div class="col-md-10 col-sm-10">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">??</span></button>
                          <p><strong>Success!</strong> <?php echo $_SESSION['pesan']?></p>
                        </div>
                      </div>
                      <?php }
                        $_SESSION['pesan'] = '';
                      ?>
                    
      
                
                    <div class="panel-body">
                      <div class="responsive-table">
                      <table id="datatables-example" class="table table-striped table-bordered" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>NO</th>
                          <th>NO. SURAT</th>
                          <th>TGL SURAT</th>
                          <th>PEMOHON</th>
                          <th>TTL</th>
                          <th>STATUS</th>
                          <th>NAMA PNS</th>
                          <th>JABATAN</th>
                          <th>TEMPAT KERJA</th>
                          <th width="18%">AKSI</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no = 1;
                        if($_GET['filter']=='ALL'){
                          $sql = mysqli_query($konek,"SELECT*FROM pen_dj AS a JOIN pegawai AS b ON a.nip=b.nip ORDER BY nosurat DESC");
                        }else{
                          $tg = explode('-', $_GET['filter']);
                          $th = $tg[0];
                          $bl = $tg[1];
                          $sql = mysqli_query($konek,"SELECT*FROM pen_dj AS a JOIN pegawai AS b ON a.nip=b.nip WHERE month(tgl_surat)='$bl' AND year(tgl_surat)='$th' ORDER BY nosurat DESC");
                        }
                        
                        foreach($sql as $pk){
                        ?>
                        <tr>
                          <td><?php echo $no++; ?></td>
                          <td><?php echo '882/'.$pk['nosurat'] ?></td>
                          <td><?php echo tgl_indo($pk['tgl_surat']) ?></td>
                          <td><?php echo $pk['nm_pmh'] ?></td>
                          <td><?php echo $pk['tlh'].', '.tgl_indo($pk['ttl']) ?></td>
                          <td><?php echo $pk['stts'] ?></td>
                          <td><?php echo $pk['nama'] ?></td>
                          <td><?php echo $pk['jbtn'] ?></td>
                          <td><?php echo $pk['tmp_kerja'] ?></td>
                          <td>
                            <a href="#" id="<?php echo $pk['nosurat'] ?>" class="btn btn-primary btn-round btn-xs modal_edit"><i class="fa fa-edit"></i></a>
                            <a class="btn btn-danger btn-xs btn-round" onclick="confirm_delete('pensiun_del?id=<?php echo $pk['nosurat'];?>')"><i class="fa fa-trash"></i></a>
                            <a href="laporan_detail_dj?ref=<?php echo $pk['nosurat'];?>" target="_blank" class="btn btn-round btn-xs btn-warning"><i class="fa fa-print"></i></a>
                          </td>
                        </tr>
                      <?php } ?>
                      </tbody>
                        </table>
                      </div>
                  </div>
                </div>

                <div class="modal fade" id="modal-pensiun">
                  <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                      <form method="post" action="pensiun_add.php" enctype="multipart/form-data">
                      <div class="modal-header bg-primary" style="border-top-left-radius: 5px;border-top-right-radius: 5px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                        <h3 class="modal-title">Tambah Data Pensiun</h3>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="row">
                                  <?php
                                    $hasil = mysqli_query($konek,"SELECT max(nosurat) as noSurat FROM pen_dj");
                                    $data  = mysqli_fetch_assoc($hasil);
                                    $no= $data['noSurat'];
                                    $noUrut= $no + 1;
                                    $th = date('Y');
                                    $tambah = sprintf("%04s",$noUrut);
                                    $no_srt = $tambah.'-Set/Disdikbud/'.$th;

                                    ?>
                                  <div class="form-group col-md-12"><label class="col-sm-2 control-label text-right">No. Surat</label>
                                      <div class="col-sm-10"><input type="text" name="no" class="form-control primary" readonly="" value="<?php echo $no_srt; ?>"></div>
                                  </div>
                                  <div class="form-group col-md-12"><label class="col-sm-2 control-label text-right">Tgl Surat</label>
                                      <div class="col-sm-10"><input type="date" name="tg_srt" class="form-control primary" required=""></div>
                                  </div>
                                  <div class="form-group col-md-12"><label class="col-sm-2 control-label text-right">Nama Pemohon</label>
                                      <div class="col-sm-10"><input type="text" name="pmh" class="form-control primary" required=""></div>
                                  </div>
                                  
                                  <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Tempat Lahir</label>
                                      <div class="col-sm-10"><input type="text" name="tmp" class="form-control primary" required=""></div>
                                  </div>
                                  <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Tanggal Lahir</label>
                                      <div class="col-sm-10"><input type="date" name="tgl" class="form-control primary" required=""></div>
                                  </div>
                                  <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Status</label>
                                      <div class="col-sm-10">
                                        <label><input type="radio" name="stts" value="Duda" required=""> Duda </label>&nbsp;&nbsp;&nbsp;
                                        <label><input type="radio" name="stts" value="Janda" required=""> Janda </label>
                                      </div>
                                  </div>
                                  <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Hubungan</label>
                                      <div class="col-sm-10">
                                        <label><input type="radio" name="hub" value="Suami" required=""> Suami </label>&nbsp;&nbsp;&nbsp;
                                        <label><input type="radio" name="hub" value="Isteri" required=""> Isteri </label>
                                      </div>
                                  </div>
                                </div>
                              </div>

                              <div class="col-md-6">
                                <div class="row">
                                <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Nama PNS</label>
                                  <div class="col-sm-10">
                                    <select name="nip" id="nip"  onchange="pegawai(this.value)" class="form-control primary pens" style="width: 100%" required="">
                                        <option selected="" disabled="">-PILIH-</option>
                                        <?php
                                        $result = mysqli_query($konek,"SELECT*FROM pegawai AS a JOIN pangkat AS b ON a.pangkat=b.id_pnkt ORDER BY a.nama");   
                                        $jsArray = "var pgh = new Array();\n";       
                                        while ($row = mysqli_fetch_array($result)) {
                                        ?>   
                                        <option value="<?php echo $row['nip'] ?>"><?php echo $row['nama'] ?></option>
                                            <?php  
                                            $jsArray .= "pgh['" . $row['nip'] . "'] = {
                                              nip:'".addslashes($row['nip'])."',
                                              nama:'".addslashes($row['nama'])."',
                                              pnkt:'".addslashes($row['nm_pnkt'])."',
                                              gol:'".addslashes($row['ket'])."',
                                              jbtn:'".addslashes($row['jbtn'])."'
                                            };\n";   
                                        }     
                                        ?>    
                                      </select>
                                  </div>
                                </div>
                                <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Pangkat Gol.</label>
                                  <div class="col-sm-10"><input type="text" name="pkt" id="pkg" class="form-control primary" required="" readonly=""></div>
                                </div>
                                <div class="form-group col-md-12"><label class="col-sm-2 control-label text-right">Jabatan</label>
                                    <div class="col-sm-10"><input type="text" name="jbt" id="jbt" class="form-control primary" required="" readonly=""></div>
                                </div>
                                <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Tanggal Wafat</label>
                                  <div class="col-sm-10"><input type="date" name="tgw" class="form-control primary" required=""></div>
                                </div>
                               <div class="form-group col-md-12"><label class="col-sm-2 control-label text-left">Pangkat Usulan</label>
                                  <div class="col-sm-10">
                                    <select name="pku" id="pku" onchange="pangkat(this.value)" class="form-control primary pens" style="width: 100%">
                                      <option selected="" disabled="">-PILIH-</option>
                                      <?php
                                          $qry = mysqli_query($konek,"SELECT*FROM pangkat ORDER BY nm_pnkt");
                                          $Array = "var pnk = new Array();\n"; 
                                          foreach($qry as $hs){
                                          ?>
                                          <option value="<?php echo $hs['id_pnkt'] ?>"><?php echo $hs['nm_pnkt'].'/'.$hs['ket'] ?></option>
                                          <?php 
                                          $Array .= "pnk['" . $hs['id_pnkt'] . "'] = {
                                              id:'".addslashes($hs['id_pnkt'])."',
                                              npk:'".addslashes($hs['nm_pnkt'])."',
                                              gpn:'".addslashes($hs['ket'])."'
                                            };\n"; 
                                        } ?>
                                    </select>
                                  </div>
                                </div>
                                <input type="hidden" name="nm_pnkt" id="nmpk" class="form-control primary" required="">
                                <input type="hidden" name="gpn" id="gpn" class="form-control primary" required="">
                                  </div>
                                </div>
                              </div>

                            </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
                        <button type="submit" class="btn btn-primary btn-round"><i class="fa fa-floppy-o"></i> Simpan</button>
                      </div>
                    </form>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <script type="text/javascript">   
                <?php echo $jsArray; ?> 
                function pegawai(nip){ 
                //document.getElementById('pkg').value = pgh[nip].nip; 
                document.getElementById('pkg').value = pgh[nip].pnkt+'/ '+pgh[nip].gol;
                document.getElementById('jbt').value = pgh[nip].jbtn;
                }; 
                </script>
                <script type="text/javascript">   
                <?php echo $Array; ?> 
                function pangkat(id_pnkt){ 
                 
                document.getElementById('nmpk').value = pnk[id_pnkt].npk;
                document.getElementById('gpn').value = pnk[id_pnkt].gpn;
                }; 
                </script>

                <div class="modal fade" id="pensiun-edit"></div>

                <!--Modal Hapus-->
                <div class="modal modal-xs fade" id="modal-delete">
                  <div class="modal-dialog">
                    <div class="modal-content" style="margin-top:150px;">
                        <div class="modal-header bg-primary" style="border-top-left-radius: 5px;border-top-right-radius: 5px;">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                            <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> KONFIRMASI</h4>
                        </div> 
                        <div class="modal-body" align="center">Apakah Anda Yakin??<br>Hapus data <i class="fa fa-trash"></i></div>   
                        <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                            <a href="#" class="btn btn-danger btn-round" id="delete-link"><i class="fa fa-check"></i> Hapus</a>
                            <button type="button" class="btn btn-success btn-round" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
                        </div>
                    </div>
                  </div>
                </div>

              </div>  
              </div>
            </div>
          <!-- end: content -->
         
      </div>

      <!-- start: Mobile -->
      <div id="mimin-mobile" class="reverse">
        <div class="mimin-mobile-menu-list">
            <div class="col-md-12 sub-mimin-mobile-menu-list animated fadeInLeft">
                <ul class="nav nav-list">
                    <li class="active ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa-home fa"></span>Dashboard 
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                          <li><a href="dashboard-v1.html">Dashboard v.1</a></li>
                          <li><a href="dashboard-v2.html">Dashboard v.2</a></li>
                      </ul>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa-diamond fa"></span>Layout
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="topnav.html">Top Navigation</a></li>
                        <li><a href="boxed.html">Boxed</a></li>
                      </ul>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa-area-chart fa"></span>Charts
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="chartjs.html">ChartJs</a></li>
                        <li><a href="morris.html">Morris</a></li>
                        <li><a href="flot.html">Flot</a></li>
                        <li><a href="sparkline.html">SparkLine</a></li>
                      </ul>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa fa-pencil-square"></span>Ui Elements
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="color.html">Color</a></li>
                        <li><a href="weather.html">Weather</a></li>
                        <li><a href="typography.html">Typography</a></li>
                        <li><a href="icons.html">Icons</a></li>
                        <li><a href="buttons.html">Buttons</a></li>
                        <li><a href="media.html">Media</a></li>
                        <li><a href="panels.html">Panels & Tabs</a></li>
                        <li><a href="notifications.html">Notifications & Tooltip</a></li>
                        <li><a href="badges.html">Badges & Label</a></li>
                        <li><a href="progress.html">Progress</a></li>
                        <li><a href="sliders.html">Sliders</a></li>
                        <li><a href="timeline.html">Timeline</a></li>
                        <li><a href="modal.html">Modals</a></li>
                      </ul>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                       <span class="fa fa-check-square-o"></span>Forms
                       <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="formelement.html">Form Element</a></li>
                        <li><a href="#">Wizard</a></li>
                        <li><a href="#">File Upload</a></li>
                        <li><a href="#">Text Editor</a></li>
                      </ul>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa fa-table"></span>Tables
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="datatables.html">Data Tables</a></li>
                        <li><a href="handsontable.html">handsontable</a></li>
                        <li><a href="tablestatic.html">Static</a></li>
                      </ul>
                    </li>
                    <li class="ripple">
                      <a href="calendar.html">
                         <span class="fa fa-calendar-o"></span>Calendar
                      </a>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa fa-envelope-o"></span>Mail
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="mail-box.html">Inbox</a></li>
                        <li><a href="compose-mail.html">Compose Mail</a></li>
                        <li><a href="view-mail.html">View Mail</a></li>
                      </ul>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa fa-file-code-o"></span>Pages
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="forgotpass.html">Forgot Password</a></li>
                        <li><a href="login.html">SignIn</a></li>
                        <li><a href="reg.html">SignUp</a></li>
                        <li><a href="article-v1.html">Article v1</a></li>
                        <li><a href="search-v1.html">Search Result v1</a></li>
                        <li><a href="productgrid.html">Product Grid</a></li>
                        <li><a href="profile-v1.html">Profile v1</a></li>
                        <li><a href="invoice-v1.html">Invoice v1</a></li>
                      </ul>
                    </li>
                     <li class="ripple"><a class="tree-toggle nav-header"><span class="fa "></span> MultiLevel  <span class="fa-angle-right fa right-arrow text-right"></span> </a>
                      <ul class="nav nav-list tree">
                        <li><a href="view-mail.html">Level 1</a></li>
                        <li><a href="view-mail.html">Level 1</a></li>
                        <li class="ripple">
                          <a class="sub-tree-toggle nav-header">
                            <span class="fa fa-envelope-o"></span> Level 1
                            <span class="fa-angle-right fa right-arrow text-right"></span>
                          </a>
                          <ul class="nav nav-list sub-tree">
                            <li><a href="mail-box.html">Level 2</a></li>
                            <li><a href="compose-mail.html">Level 2</a></li>
                            <li><a href="view-mail.html">Level 2</a></li>
                          </ul>
                        </li>
                      </ul>
                    </li>
                    <li><a href="credits.html">Credits</a></li>
                  </ul>
            </div>
        </div>       
      </div>
      <button id="mimin-mobile-menu-opener" class="animated rubberBand btn btn-circle btn-danger">
        <span class="fa fa-bars"></span>
      </button>
       <!-- end: Mobile -->
<?php include "footer.php"; ?>