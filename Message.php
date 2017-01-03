<?php
/**
 * Created by PhpStorm.
 * User: HocineFR
 * Date: 03-01-2017
 * Time: 19:47
 */

namespace hocine\protun\core;


class Message
{
    public static $noUser = "لا يوجد مستخدم يطابق بحثك";
    public static $loginError = "اسم المستخدم أو كلمة المرور غير صحيحة";
    public static $postNotFound = "عفوا ... هذا الموضوع قد تم حذفه أو نقله إلى قسم آخر";
    public static $deleteFailure = " للأسف ... فشلت عملية الحذف و ذلك للأسباب الآتية:";
    public static $editFailure = "للأسف ... فشلت عملية التعديلو ذلك للأسباب الآتية:  ";
    public static $createFailure = "للأسف ... فشلت عملية الانشاء و ذلك للأسباب الآتية:";

}