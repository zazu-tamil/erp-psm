<!-- Left side column. contains the logo and sidebar -->

<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php  if ($this->session->userdata(SESS_HD . 'emp_photo') == '') { ?>
                <img src="<?php echo base_url('asset/images/user.jpg') ?>" class="img-circle" alt="User Image">
                <?php } else { ?>
                <!-- <img src="<?php echo ('../../payroll/' . $this->session->userdata(SESS_HD . 'emp_photo')); ?>"
                    class="img-circle" alt="User Image"> -->
                <img src="<?php echo base_url($this->session->userdata(SESS_HD . 'emp_photo')); ?>" class="img-circle"
                    alt="<?php echo strtolower($this->session->userdata(SESS_HD . 'user_name')); ?>">
                <?php } ?>
            </div>
            <div class="pull-left info">
                <p class="text-capitalize text-sm">
                    <?php echo strtolower($this->session->userdata(SESS_HD . 'user_name')); ?>
                </p>
                <a href="#"><i class="fa fa-circle text-success"></i>
                    <?php echo ($this->session->userdata(SESS_HD . 'user_type')); ?> </a>

            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php if ($this->session->userdata(SESS_HD . 'user_type') == 'Admin') { ?>
        <?php include_once('admin-menu.php'); ?>
        <?php } ?>

        <?php if ($this->session->userdata(SESS_HD . 'user_type') == 'Manager') { ?>
        <?php include_once('manager-menu.php'); ?>
        <?php } ?>


        <?php if ($this->session->userdata(SESS_HD . 'user_type') == 'Staff' && ($this->session->userdata(SESS_HD . 'employee_category') == 'Teaching Staff')) { ?>
        <?php include_once('staff-menu.php'); ?>
        <?php } ?>

        <?php if ($this->session->userdata(SESS_HD . 'user_type') == 'Canteen') { ?>
        <?php include_once('canteen-menu.php'); ?>
        <?php } ?>
        <?php if ($this->session->userdata(SESS_HD . 'user_type') == 'Parent') { ?>
        <?php include_once('parent-menu.php'); ?>
        <?php } ?>
        <?php if ($this->session->userdata(SESS_HD . 'user_type') == 'Student') { ?>
        <?php include_once('student-menu.php'); ?>
        <?php } ?>

        <?php if ($this->session->userdata(SESS_HD . 'user_type') == 'Staff' && ($this->session->userdata(SESS_HD . 'employee_category') == 'Administration')) { ?>
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">Dashboard</li>
            <li <?php if ($this->uri->segment(1, 0) === 'dash')
                    echo 'class="active"'; ?>><a href="<?php echo site_url('dash') ?>"><i class="fa fa-dashboard"></i>
                    <span>Dashboard</span></a>
            </li>

            <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('montessori-lesson-plan','montessori-lesson-box','group-list','montessori-area-syllabus','class-environment-progress','group-environment-progress'
)))
                            echo 'active'; ?>">
                <a href="#"><i class="fa fa-book"></i>Environment Area Plan
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if ($this->uri->segment(1, 0) === 'montessori-lesson-plan')
                                    echo 'class="active"'; ?>><a
                            href="<?php echo site_url('montessori-lesson-plan') ?>"><i class="fa fa-globe"></i>
                            Area Progress</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'montessori-lesson-box')
                                    echo 'class="active"'; ?>><a
                            href="<?php echo site_url('montessori-lesson-box') ?>"><i class="fa fa-globe"></i>
                            Area Plan Box</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'group-list')
                                    echo 'class="active"'; ?>><a href="<?php echo site_url('group-list') ?>"><i
                                class="fa fa-globe"></i> Group
                            Creation</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'montessori-area-syllabus')
                                    echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('montessori-area-syllabus') ?>"><i class="fa fa-clipboard"></i>
                            Area Syllabus List</a>
                    </li>

                    <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('class-environment-progress','group-environment-progress')))
                    echo 'active'; ?>">
                        <a href="#"><i class="fa fa-file-text"></i>Reports
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($this->uri->segment(1, 0) === 'class-environment-progress')
                            echo 'class="active"'; ?>>
                                <a href="<?php echo site_url('class-environment-progress') ?>"><i
                                        class="fa fa-file-text"></i>
                                    Class Env.Progress</a>
                            </li>
                            <li <?php if ($this->uri->segment(1, 0) === 'group-environment-progress')
                            echo 'class="active"'; ?>>
                                <a href="<?php echo site_url('group-environment-progress') ?>"><i
                                        class="fa fa-file-text"></i>
                                    Group Env.Progress</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </li>

            <li class="header">Admission</li>

            <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('admission-enquiry-list')))
                    echo 'active'; ?>">
                <a href="#">
                    <i class="fa fa-hand-paper-o"></i> <span>Enquiry</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if ($this->uri->segment(1, 0) === 'admission-enquiry-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('admission-enquiry-list') ?>"><i
                                class="fa fa-envelope"></i>
                            Admission Enquiry List</a></li>

                </ul>
            </li>

            <li class="header">Student Info</li>

            <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('student-list', 'add-student', 'group-list', 'group-info', 'edit-student', 'student-class-shuffle', 'student-branch-shuffle', 'student-section-shuffle', 'student-promotion')))
                    echo 'active'; ?>">
                <a href="#">
                    <i class="fa fa-hand-paper-o"></i> <span>Student</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <li <?php if ($this->uri->segment(1, 0) === 'add-student')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('add-student') ?>"><i
                                class="fa fa-envelope"></i> Add Student
                            Info</a></li>
                    <li <?php if ($this->uri->segment(1, 0) === 'student-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('student-list') ?>"><i
                                class="fa fa-envelope"></i> Student
                            List</a></li>
                    <li <?php if ($this->uri->segment(1, 0) === 'group-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('group-list') ?>"><i
                                class="fa fa-envelope"></i> Group List</a>
                    </li>

                    <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('student-class-shuffle', 'student-branch-shuffle', 'student-section-shuffle')))
                            echo 'active'; ?>">
                        <a href="#"><i class="fa fa-envelope"></i>Student Shuffle
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($this->uri->segment(1, 0) === 'student-branch-shuffle')
                                    echo 'class="active"'; ?>>
                                <a href="<?php echo site_url('student-branch-shuffle') ?>"><i
                                        class="fa fa-circle-o"></i> Branch Shuffle</a>
                            </li>
                            <li <?php if ($this->uri->segment(1, 0) === 'student-class-shuffle')
                                    echo 'class="active"'; ?>>
                                <a href="<?php echo site_url('student-class-shuffle') ?>"><i class="fa fa-circle-o"></i>
                                    Class Shuffle</a>
                            </li>
                            <li <?php if ($this->uri->segment(1, 0) === 'student-section-shuffle')
                                    echo 'class="active"'; ?>>
                                <a href="<?php echo site_url('student-section-shuffle') ?>"><i
                                        class="fa fa-circle-o"></i> Section Shuffle</a>
                            </li>
                        </ul>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'student-promotion')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('student-promotion') ?>"><i
                                class="fa fa-envelope"></i>Student
                            Promotion</a></li>


                </ul>
            </li>
            <li class="header">Fees</li>
            <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('fee-plan-class-list')))
                    echo 'active'; ?>">
                <a href="#">
                    <i class="fa fa-hand-paper-o"></i> <span>Fees</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if ($this->uri->segment(1, 0) === 'fee-plan-class-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('fee-plan-class-list') ?>"><i
                                class="fa fa-envelope"></i>
                            Fee Plan Class List</a></li>

                </ul>
            </li>
            <li class="header">Syllabus</li>
            <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('class-subject-list', 'class-subject-info')))
                    echo 'active'; ?>">
                <a href="#">
                    <i class="fa fa-hand-paper-o"></i> <span>Class Syllabus</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if ($this->uri->segment(1, 0) === 'class-subject-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('class-subject-list') ?>"><i
                                class="fa fa-envelope"></i>
                            Class Subject List</a></li>

                </ul>
            </li>
            <li class="header">Lesson Plan</li>
            <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('lesson-plan-list', 'lesson-plan-info', 'lesson-evaluation-list')))
                    echo 'active'; ?>">
                <a href="#">
                    <i class="fa fa-hand-paper-o"></i> <span>Lesson Plan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if ($this->uri->segment(1, 0) === 'lesson-plan-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('lesson-plan-list') ?>"><i
                                class="fa fa-envelope"></i>
                            Lesson Plan List</a></li>
                    <!-- <li <?php if ($this->uri->segment(1, 0) === 'lesson-evaluation-list')
                            echo 'class="active"'; ?>><a
                            href="<?php echo site_url('lesson-evaluation-list') ?>"><i class="fa fa-envelope"></i>
                            Lesson Evaluation List</a></li> -->

                </ul>
            </li>

            <li class="header">Masters</li>
            <li class="treeview <?php if (in_array($this->uri->segment(1, 0), array('academic-year-list', 'admission-type-list', 'adm-enquiry-status-list', 'class-list', 'class-category-list', 'class-section-list', 'enquiry-source-list', 'school-branch-list', 'blood-group-list', 'group-type-list', 'gender-list', 'promotion-status-list', 'fee-type-list', 'fee-plan-list', 'document-type-list')))
                    echo 'active'; ?>">
                <a href="#">
                    <i class="fa fa-rupee"></i> <span>Masters</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">

                    <li <?php if ($this->uri->segment(1, 0) === 'academic-year-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('academic-year-list') ?>"><i class="fa fa-briefcase"></i>
                            Academic Year List</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'admission-type-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('admission-type-list') ?>"><i class="fa fa-briefcase"></i>
                            Admission Type List</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'class-category-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('class-category-list') ?>"><i class="fa fa-briefcase"></i>
                            Class Category List</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'class-section-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('class-section-list') ?>"><i class="fa fa-briefcase"></i>
                            Class Section List</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'adm-enquiry-status-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('adm-enquiry-status-list') ?>"><i class="fa fa-briefcase"></i>
                            Enquiry Status List</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'class-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('class-list') ?>"><i
                                class="fa fa-briefcase"></i> Class
                            List</a></li>
                    <li <?php if ($this->uri->segment(1, 0) === 'enquiry-source-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('enquiry-source-list') ?>"><i class="fa fa-briefcase"></i>Enquiry
                            Source List</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'school-branch-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('school-branch-list') ?>"><i class="fa fa-briefcase"></i>School
                            Branch List</a>
                    </li>
                    <li <?php if ($this->uri->segment(1, 0) === 'blood-group-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('blood-group-list') ?>"><i
                                class="fa fa-briefcase"></i>
                            Blood Group List</a></li>
                    <li <?php if ($this->uri->segment(1, 0) === 'group-type-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('group-type-list') ?>"><i
                                class="fa fa-briefcase"></i>
                            Environment Type</a></li>

                    <li <?php if ($this->uri->segment(1, 0) === 'gender-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('gender-list') ?>"><i
                                class="fa fa-briefcase"></i>
                            Gender List</a></li>

                    <li <?php if ($this->uri->segment(1, 0) === 'promotion-status-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('promotion-status-list') ?>"><i
                                class="fa fa-briefcase"></i>
                            Promotion Status List</a></li>

                    <li <?php if ($this->uri->segment(1, 0) === 'fee-type-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('fee-type-list') ?>"><i
                                class="fa fa-briefcase"></i>
                            Fee Type List</a></li>


                    <li <?php if ($this->uri->segment(1, 0) === 'document-type-list')
                            echo 'class="active"'; ?>><a href="<?php echo site_url('document-type-list') ?>"><i
                                class="fa fa-briefcase"></i>
                            Document Type</a></li>

                    <hr />
                    <li <?php if ($this->uri->segment(1, 0) === 'dyn-fld-option-list')
                            echo 'class="active"'; ?>>
                        <a href="<?php echo site_url('dyn-fld-option-list') ?>"><i class="fa fa-briefcase"></i>
                            Dynamic
                            Field Option </a>
                    </li>
                    <hr />
                </ul>
            </li>

            <li class="header"></li>
            <li><a href="<?php echo site_url('change-password') ?>"><i class="fa fa-empire"></i> <span>Change
                        Password</span></a></li>
            <li class="header"></li>
            <li><a href="<?php echo site_url('logout') ?>"><i class="fa fa-sign-out"></i> <span>Sign
                        Out</span></a></li>
        </ul>
        <?php } ?>
    </section>
    <!-- /.sidebar -->
</aside>